import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.sql.*;
import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.stream.JsonWriter;
import com.google.*;

import org.w3c.dom.Document;

import org.bson.*;
import org.bson.types.ObjectId;

import com.mongodb.*;

import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoDatabase;
import com.mongodb.util.JSON;


public class Importer {

    private static String folder="data/";

    private static String database = "jdbc:mysql://localhost/kis";
    private static String user = "root";
    private static String pass = "";
    private static Connection con = null;
    private static PreparedStatement pStmt = null;
    
    private static Map<String, String> nutzer = new HashMap<String, String>();


    public static void main(String args[]) {

        String input = "";
        Boolean go = true;

        BufferedReader in = new BufferedReader(new InputStreamReader(System.in));

        while (go) {
            System.out.print("CMDs (convert, quit) : ");
            try {
                input = in.readLine();
            } catch (IOException e) {
                System.out.println("error in input");
            }
            try {
                switch (input) {
                    case "convert":
                        System.out.print("Inserts (all, benutzer, gruppe, nachrichten, veranstaltung) : ");
                        try {
                            input = in.readLine();
                        } catch (IOException e) {
                            System.out.println("error in input");
                        }
                        switch (input) {
                            case "benutzer":
                                importBenutzer();
                                break;
                            case "gruppe":
                                importGruppe();
                                break;
                            case "nachrichten":
                                importNachrichten();
                                break;
                            case "veranstaltung":
                                importVeranstaltung();
                                break;
                            
                            case "all":
                                importBenutzer();
                                importGruppe();
                                importNachrichten();
                                importVeranstaltung();
                                
                                break;
                            default:
                                break;
                        }
                        break;
                    case "quit":
                        go = false;
                        break;
                    default:
                        break;
                }
            } catch(Exception e){
               e.printStackTrace();
               System.out.println("Fehlerhafte Eingabe!");
            }
        }
    }
    
    private static void importBenutzer() throws Exception {
      
        
        String query = "SELECT nutzerID FROM benutzer";
        String adminquery ="SELECT nutzerID FROM admin";
        

        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);
        
        Statement st1 = con.createStatement();
        Statement st2 = con.createStatement();
        ResultSet rs = st1.executeQuery(query);
        
        while(rs.next()){
        	ObjectId id = new ObjectId();
            String value = id.toString();
            nutzer.put(rs.getString(1), value);
        }
        
        query = "SELECT * FROM benutzer";
         rs = st1.executeQuery(query);
        
        
        
        ResultSet rsadmin= st2.executeQuery(adminquery);
        List<String> admins = new ArrayList<String>();
        while(rsadmin.next()){
        	admins.add(rsadmin.getString(1));
        }
        
       // JSON Objekt von result set erzeugen
        
        
        List<JsonObject> resList = new ArrayList<JsonObject>();
        try {
            // get column names
            ResultSetMetaData rsMeta = rs.getMetaData();
            int columnCnt = rsMeta.getColumnCount();
            List<String> columnNames = new ArrayList<String>();
            for(int i=1;i<=columnCnt;i++) {
                columnNames.add(rsMeta.getColumnName(i).toUpperCase());
            }
            
            while(rs.next()) { // convert each object to an human readable JSON object
            	JsonObject obj = new JsonObject();
                for(int i=1;i<=columnCnt;i++) {
                    
                    if(i==1){
                    	String key = "_id";
                        String value = nutzer.get(rs.getString(i));
                        obj.addProperty(key, value);
                    	
                    }else{
                    	String key = columnNames.get(i - 1);
                        String value = rs.getString(i);
                        obj.addProperty(key, value);
                    }
                }
                // Freunde zu benutzer hinzufügen
                Statement st3 = con.createStatement();
                String friendquery ="SELECT nutzerID2 FROM istbefreundet WHERE nutzerID1 ='"+rs.getString(1)+"'";
                ResultSet rsfriends= st3.executeQuery(friendquery);
                List<String> friends = new ArrayList<String>();
                JsonArray jArray = new JsonArray();
                while(rsfriends.next()){
                	JsonObject json = new JsonObject();
                	json.addProperty("nutzerID",nutzer.get(rsfriends.getString(1)));
                	jArray.add(json);
                }
                obj.add("FRIENDS", jArray);

                // isAdmin zu benutzer document hinzufügen
                boolean isAdmin=false;
                for(int i=0;i<admins.size();i++){
                	if(rs.getString(1).equals(admins.get(i))){
                		isAdmin=true;
                	}
                }
                if(isAdmin==true){
                	String key = "ADMIN";
                    String value ="true";
                    obj.addProperty(key, value);
                }else{
                	String key = "ADMIN";
                    String value ="false";
                    obj.addProperty(key, value);
                }
                
                resList.add(obj);
            }
        } catch(Exception e) {
            e.printStackTrace();
        } finally {
            try {
                rs.close();
            } catch (SQLException e) {
                e.printStackTrace();
            }
        }

        MongoClientURI connectionString = new MongoClientURI("mongodb://localhost:27017");
        MongoClient mongo = new MongoClient( connectionString );
        MongoDatabase db =  mongo.getDatabase("ise");
        MongoCollection<org.bson.Document> collection = db.getCollection("benutzer");
        
        collection.createIndex(new BasicDBObject("EMAIL", 1)); // damit email unique ist
        
		// add Json List to mongodb
		for(int i = 0;i<resList.size();i++){
			org.bson.Document doc = new org.bson.Document();
			collection.insertOne(doc.parse(resList.get(i).toString()));	
		}

		System.out.println("Done benutzer");

        // clean up connections

        con.close();

    }
    
    private static void importGruppe() throws Exception{
       
    	String query = "SELECT * FROM gruppe";
        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);
        
        Statement st1 = con.createStatement();
        ResultSet rs = st1.executeQuery(query);

       // JSON Objekt von result set erzeugen

        List<JsonObject> resList = new ArrayList<JsonObject>();
        try {
            // get column names
            ResultSetMetaData rsMeta = rs.getMetaData();
            int columnCnt = rsMeta.getColumnCount();
            List<String> columnNames = new ArrayList<String>();
            for(int i=1;i<=columnCnt;i++) {
                columnNames.add(rsMeta.getColumnName(i).toUpperCase());
            }
            
            while(rs.next()) { // convert each object to an human readable JSON object
            	JsonObject obj = new JsonObject();
                for(int i=1;i<=columnCnt;i++) {
                    
                    if(i==1){
                    	String key = "_id";
                    	ObjectId id = new ObjectId();
                        String value = id.toString();
                        obj.addProperty(key, value);
                    	
                    }else{
                    	
                    	String key = columnNames.get(i - 1);
                    	String value="";
                    	if(key.equals("NUTZERID")){
                    		 value = nutzer.get(rs.getString(i));
                    	}else{
                    		value = rs.getString(i);
                    	}
                        obj.addProperty(key, value);
                    }
                }
                
                // Mitglieder zu Gruppe hinzufügen
                Statement st3 = con.createStatement();
                String friendquery ="SELECT nutzerID FROM istmitglied WHERE gruppenID ='"+rs.getString(1)+"'";
                ResultSet rsfriends= st3.executeQuery(friendquery);
                List<String> friends = new ArrayList<String>();
                JsonArray jArray = new JsonArray();
                while(rsfriends.next()){
                	JsonObject json = new JsonObject();
                	json.addProperty("NUTZERID",nutzer.get(rsfriends.getString(1)));
                	jArray.add(json);
                }
                obj.add("MITGLIEDER", jArray);

                // Beitraege zu Gruppe hinzufügen
                Statement st4 = con.createStatement();
                String beitragquery ="SELECT beitragsID,bTitel,bInhalt,nutzerID FROM beitrag WHERE gruppenID ='"+rs.getString(1)+"'";
                ResultSet rsbeitrag= st4.executeQuery(beitragquery);
                List<String> beitraege = new ArrayList<String>();
                
                ResultSetMetaData rsMeta1 = rsbeitrag.getMetaData();
                int columnCnt1 = rsMeta1.getColumnCount();
                List<String> columnNames1 = new ArrayList<String>();
                for(int i=1;i<=columnCnt1;i++) {
                    columnNames1.add(rsMeta1.getColumnName(i).toUpperCase());
                }
                JsonArray jArrayb = new JsonArray();
                while(rsbeitrag.next()){
                	JsonObject json1 = new JsonObject();
                	for(int i=1;i<=columnCnt1;i++) {
                        
                        if(i==1){
                        	String key = "_id";
                        	ObjectId id = new ObjectId();
                            String value = id.toString();
                            json1.addProperty(key, value);
                        	
                        }else{
                        	String key = columnNames1.get(i - 1);
                        	String value="";
                        	if(key.equals("NUTZERID")){
                        		 value = nutzer.get(rsbeitrag.getString(i));
                        	}else{
                        		value = rsbeitrag.getString(i);
                        	}
                            json1.addProperty(key, value);
                        }
                    }
                	jArrayb.add(json1);
                }
                obj.add("BEITRAEGE", jArrayb);
                resList.add(obj);
            }
        } catch(Exception e) {
            e.printStackTrace();
        } finally {
            try {
                rs.close();
            } catch (SQLException e) {
                e.printStackTrace();
            }
        }

        MongoClientURI connectionString = new MongoClientURI("mongodb://localhost:27017");
        MongoClient mongo = new MongoClient( connectionString );
        MongoDatabase db =  mongo.getDatabase("ise");
        MongoCollection<org.bson.Document> collection = db.getCollection("gruppe");
        
        //collection.createIndex(new BasicDBObject("email", 1)); // damit email unique ist
        
		// add Json List to mongodb
		for(int i = 0;i<resList.size();i++){
			org.bson.Document doc = new org.bson.Document();
			collection.insertOne(doc.parse(resList.get(i).toString()));	
		}

		System.out.println("Done beitraege");

        // clean up connections

        con.close();

    }

    private static void importNachrichten() throws Exception {
    	String query = "SELECT * FROM unterhaltung";
        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);
        
        Statement st1 = con.createStatement();
        ResultSet rs = st1.executeQuery(query);

       // JSON Objekt von result set erzeugen

        List<JsonObject> resList = new ArrayList<JsonObject>();
        try {
            // get column names
            ResultSetMetaData rsMeta = rs.getMetaData();
            int columnCnt = rsMeta.getColumnCount();
            List<String> columnNames = new ArrayList<String>();
            for(int i=1;i<=columnCnt;i++) {
                columnNames.add(rsMeta.getColumnName(i).toUpperCase());
            }
            
            while(rs.next()) { // convert each object to an human readable JSON object
            	JsonObject obj = new JsonObject();
                for(int i=1;i<=columnCnt;i++) {
                    
                    if(i==1){
                    	String key = "_id";
                    	ObjectId id = new ObjectId();
                        String value = id.toString();
                        obj.addProperty(key, value);
                    	
                    }else{
                    	String key = columnNames.get(i - 1);
                    	String value="";
                    	if(key.equals("NUTZERID")){
                    		 value = nutzer.get(rs.getString(i));
                    	}else{
                    		value = rs.getString(i);
                    	}
                        obj.addProperty(key, value);
                    }
                }
                
                // Mitglieder zu Konversation hinzufügen
                Statement st3 = con.createStatement();
                String fuehrenquery ="SELECT nutzerID1,nutzerID2 FROM fuehren WHERE unterhaltungsID ='"+rs.getString(1)+"'";
                ResultSet rsfuehren= st3.executeQuery(fuehrenquery);
                if(rsfuehren.isBeforeFirst()){
                	rsfuehren.next();
                	obj.addProperty("NUTZERID1",nutzer.get(rsfuehren.getString(1)));
                	
                	obj.addProperty("NUTZERID2",nutzer.get(rsfuehren.getString(2)));
                }
                	
                // nachrichten zu Unterhaltung hinzufügen
                Statement st4 = con.createStatement();
                String nachrichtquery ="SELECT * FROM nachricht WHERE unterhaltungsID ='"+rs.getString(1)+"'";
                ResultSet rsbeitrag= st4.executeQuery(nachrichtquery);

                ResultSetMetaData rsMeta1 = rsbeitrag.getMetaData();
                int columnCnt1 = rsMeta1.getColumnCount();
                List<String> columnNames1 = new ArrayList<String>();
                for(int i=1;i<=columnCnt1;i++) {
                    columnNames1.add(rsMeta1.getColumnName(i).toUpperCase());
                }
                JsonArray jArrayb = new JsonArray();
                while(rsbeitrag.next()){
                	JsonObject json1 = new JsonObject();
                	for(int i=1;i<=columnCnt1;i++) {
                        
                        if(i==2){
                        	String key = "_id";
                        	ObjectId id = new ObjectId();
                            String value = id.toString();
                            json1.addProperty(key, value);
                        	
                        }else if(i!=1){
                        	String key = columnNames1.get(i - 1);
                        	String value="";
                        	
                        	if(key.equals("EMPFAENGERID")){
                       		 	value = nutzer.get(rsbeitrag.getString(i));
	                       	}else{
	                       		if(key.equals("ERSTELLERID")){
	                        		value = nutzer.get(rsbeitrag.getString(i));
	                        	}else{
	                        		value = rsbeitrag.getString(i);
	                        	}
	                       	}
                        	
                        	
                            json1.addProperty(key, value);
                        }
                    }
                	jArrayb.add(json1);
                }
                obj.add("NACHRICHTEN", jArrayb);
                resList.add(obj);
            }
        } catch(Exception e) {
            e.printStackTrace();
        } finally {
            try {
                rs.close();
            } catch (SQLException e) {
                e.printStackTrace();
            }
        }

        MongoClientURI connectionString = new MongoClientURI("mongodb://localhost:27017");
        MongoClient mongo = new MongoClient( connectionString );
        MongoDatabase db =  mongo.getDatabase("ise");
        MongoCollection<org.bson.Document> collection = db.getCollection("nachrichten");
        
        //collection.createIndex(new BasicDBObject("email", 1)); // damit email unique ist
        
		// add Json List to mongodb
		for(int i = 0;i<resList.size();i++){
			org.bson.Document doc = new org.bson.Document();
			collection.insertOne(doc.parse(resList.get(i).toString()));	
		}

		System.out.println("Done nachrichten");

        // clean up connections

        con.close();
    }
    
    private static void importVeranstaltung() throws Exception{
    	String query = "SELECT * FROM veranstaltung";
        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);
        
        Statement st1 = con.createStatement();
        ResultSet rs = st1.executeQuery(query);

       // JSON Objekt von result set erzeugen

        List<JsonObject> resList = new ArrayList<JsonObject>();
        try {
            // get column names
            ResultSetMetaData rsMeta = rs.getMetaData();
            int columnCnt = rsMeta.getColumnCount();
            List<String> columnNames = new ArrayList<String>();
            for(int i=1;i<=columnCnt;i++) {
                columnNames.add(rsMeta.getColumnName(i).toUpperCase());
            }
            
            while(rs.next()) { // convert each object to an human readable JSON object
            	JsonObject obj = new JsonObject();
                for(int i=1;i<=columnCnt;i++) {
                    
                    if(i==1){
                    	String key = "_id";
                    	ObjectId id = new ObjectId();
                        String value = id.toString();
                        obj.addProperty(key, value);
                    	
                    }else{
                    	String key = columnNames.get(i - 1);
                    	String value="";
                    	if(key.equals("NUTZERID")){
                    		 value = nutzer.get(rs.getString(i));
                    	}else{
                    		value = rs.getString(i);
                    	}
                        obj.addProperty(key, value);
                    }
                }
	
                // zusagen/absagen/einladungen zu Unterhaltung hinzufügen
                Statement st4 = con.createStatement();
                String nachrichtquery ="SELECT nutzerID,status FROM n_beantwortet_v WHERE veranstaltungsID ='"+rs.getString(1)+"'";
                ResultSet rsbeitrag= st4.executeQuery(nachrichtquery);

                ResultSetMetaData rsMeta1 = rsbeitrag.getMetaData();
                int columnCnt1 = rsMeta1.getColumnCount();
                List<String> columnNames1 = new ArrayList<String>();
                for(int i=1;i<=columnCnt1;i++) {
                    columnNames1.add(rsMeta1.getColumnName(i).toUpperCase());
                }
                JsonArray jArrayb = new JsonArray();
                while(rsbeitrag.next()){
                	JsonObject json1 = new JsonObject();
                	for(int i=1;i<=columnCnt1;i++) {
                        	String key = columnNames1.get(i - 1);
                        	String value="";
                        	if(key.equals("NUTZERID")){
                        		 value = nutzer.get(rsbeitrag.getString(i));
                        	}else{
                        		value = rsbeitrag.getString(i);
                        	}
                            json1.addProperty(key, value);
                    }
                	jArrayb.add(json1);
                }
                obj.add("EINLADUNGEN", jArrayb);
                resList.add(obj);
            }
        } catch(Exception e) {
            e.printStackTrace();
        } finally {
            try {
                rs.close();
            } catch (SQLException e) {
                e.printStackTrace();
            }
        }

        MongoClientURI connectionString = new MongoClientURI("mongodb://localhost:27017");
        MongoClient mongo = new MongoClient( connectionString );
        MongoDatabase db =  mongo.getDatabase("ise");
        MongoCollection<org.bson.Document> collection = db.getCollection("veranstaltungen");
        
        //collection.createIndex(new BasicDBObject("email", 1)); // damit email unique ist
        
		// add Json List to mongodb
		for(int i = 0;i<resList.size();i++){
			org.bson.Document doc = new org.bson.Document();
			collection.insertOne(doc.parse(resList.get(i).toString()));	
		}

		System.out.println("Done veranstaltungen");

        // clean up connections

        con.close();
    }
}