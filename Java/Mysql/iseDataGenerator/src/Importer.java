import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.sql.*;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;

public class Importer {

    private static String folder="data/";

    private static String database = "jdbc:mysql://localhost/kis";
    private static String user = "root";
    private static String pass = "";
    private static Connection con = null;
    private static PreparedStatement pStmt = null;

    private static String insertBenutzer="benutzer.csv";
    private static String insertGruppe="gruppe.csv";
    private static String insertBeitrag="beitrag.csv";
    private static String insertVeranstaltung="veranstaltung.csv";
    private static String insertUnterhaltung="unterhaltung.csv";
    private static String insertAdmin="admin.csv";
    private static String insertNachricht="nachricht.csv";
    private static String insertIstMitglied ="istMitglied.csv";
    private static String insertIstBefreundet = "istBefreundet.csv";
    private static String insertNBeantwortetV="n_beantwortet_v.csv";
    private static String insertFuehren = "fuehren.csv";


    public static void main(String args[]) {

        String input = "";
        Boolean go = true;

        BufferedReader in = new BufferedReader(new InputStreamReader(System.in));

        while (go) {
            System.out.print("CMDs (insert, quit) : ");
            try {
                input = in.readLine();
            } catch (IOException e) {
                System.out.println("error in input");
            }
            try {
                switch (input) {
                    case "insert":
                        System.out.print("Inserts (all, benutzer, gruppe, beitrag, veranstaltung, unterhaltung, admin, nachricht, istMitglied, istBefreundet, " +
                                "nBeantwortetV, fuehren) : ");
                        try {
                            input = in.readLine();
                        } catch (IOException e) {
                            System.out.println("error in input");
                        }
                        switch (input) {
                            case "benutzer":
                                importBenutzer(insertBenutzer);
                                break;
                            case "gruppe":
                                importGruppe(insertGruppe);
                                break;
                            case "beitrag":
                                importBeitrag(insertBeitrag);
                                break;
                            case "veranstaltung":
                                importVeranstaltung(insertVeranstaltung);
                                break;
                            case "unterhaltung":
                                importUnterhaltung(insertUnterhaltung);
                                break;
                            case "admin":
                                importAdmin(insertAdmin);
                                break;
                            case "nachricht":
                                importNachricht(insertNachricht);
                                break;
                            case "istMitglied":
                                importIstMitglied(insertIstMitglied);
                                break;
                            case "istBefreundet":
                                importIstBefreundet(insertIstBefreundet);
                                break;
                            case "nBeantwortetV":
                                importNBeantwortetV(insertNBeantwortetV);
                                break;
                            case "fuehren":
                                importFuehren(insertFuehren);
                                break;
                            case "all":
                                importBenutzer(insertBenutzer);
                                importGruppe(insertGruppe);
                                importBeitrag(insertBeitrag);
                                importVeranstaltung(insertVeranstaltung);
                                importUnterhaltung(insertUnterhaltung);
                                importAdmin(insertAdmin);
                                importNachricht(insertNachricht);
                                importIstMitglied(insertIstMitglied);
                                importIstBefreundet(insertIstBefreundet);
                                importNBeantwortetV(insertNBeantwortetV);
                                importFuehren(insertFuehren);
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
               // System.out.println("Fehlerhafte Eingabe!");
            }
        }
    }

    private static void importFuehren(String insertFuehren) throws Exception {
        FileReader inputFile = new FileReader(folder + insertFuehren);
        BufferedReader bufferReader = new BufferedReader(inputFile);
        String line;

        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);

        bufferReader.readLine();
        while ((line = bufferReader.readLine()) != null) {

            String[] parts = line.split(";");

            pStmt = con.prepareStatement("insert into  kis.fuehren(unterhaltungsID, nutzerID1, nutzerID2) values (?, ?, ?)");
            for(int i = 0; i<3;i++){
                pStmt.setString(i+1,parts[i]);
            }
            pStmt.executeUpdate();
        }

        // clean up connections
        pStmt.close();
        con.close();
        //Close the buffer reader
        bufferReader.close();
    }

    private static void importNBeantwortetV(String insertNBeantwortetV) throws Exception {
        FileReader inputFile = new FileReader(folder + insertNBeantwortetV);
        BufferedReader bufferReader = new BufferedReader(inputFile);
        String line;

        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);

        bufferReader.readLine();
        while ((line = bufferReader.readLine()) != null) {

            String[] parts = line.split(";");

            pStmt = con.prepareStatement("insert into  kis.n_beantwortet_v(nutzerID, veranstaltungsID, status) values (?, ?, ?)");
            for(int i = 0; i<3;i++){
                pStmt.setString(i+1,parts[i]);
            }
            pStmt.executeUpdate();
        }

        // clean up connections
        pStmt.close();
        con.close();
        //Close the buffer reader
        bufferReader.close();
    }

    private static void importIstBefreundet(String insertIstBefreundet) throws Exception {
        FileReader inputFile = new FileReader(folder + insertIstBefreundet);
        BufferedReader bufferReader = new BufferedReader(inputFile);
        String line;

        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);

        bufferReader.readLine();
        while ((line = bufferReader.readLine()) != null) {

            String[] parts = line.split(";");

            pStmt = con.prepareStatement("insert into  kis.istbefreundet(nutzerID1, nutzerID2) values (?, ?)");
            for(int i = 0; i<2;i++){
                pStmt.setString(i+1,parts[i]);
            }
            pStmt.executeUpdate();
        }

        // clean up connections
        pStmt.close();
        con.close();
        //Close the buffer reader
        bufferReader.close();
    }

    private static void importIstMitglied(String insertIstMitglied) throws Exception {
        FileReader inputFile = new FileReader(folder + insertIstMitglied);
        BufferedReader bufferReader = new BufferedReader(inputFile);
        String line;

        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);

        bufferReader.readLine();
        while ((line = bufferReader.readLine()) != null) {

            String[] parts = line.split(";");

            pStmt = con.prepareStatement("insert into  kis.istmitglied(nutzerID, gruppenID) values (?, ?)");
            for(int i = 0; i<2;i++){
                pStmt.setString(i+1,parts[i]);
            }
            pStmt.executeUpdate();
        }

        // clean up connections
        pStmt.close();
        con.close();
        //Close the buffer reader
        bufferReader.close();
    }

    private static void importNachricht(String insertNachricht) throws Exception {
        FileReader inputFile = new FileReader(folder + insertNachricht);
        BufferedReader bufferReader = new BufferedReader(inputFile);
        String line;

        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);

        bufferReader.readLine();
        while ((line = bufferReader.readLine()) != null) {

            String[] parts = line.split(";");

            pStmt = con.prepareStatement("insert into  kis.nachricht(unterhaltungsID, empfaengerID, nInhalt, erstellerID)values (?, ?, ?, ?)");
            for(int i = 0; i<4;i++){
                pStmt.setString(i+1,parts[i]);
            }
            pStmt.executeUpdate();

        }

        // clean up connections
        pStmt.close();
        con.close();
        //Close the buffer reader
        bufferReader.close();
    }

    private static void importAdmin(String insertAdmin) throws Exception {
        FileReader inputFile = new FileReader(folder + insertAdmin);
        BufferedReader bufferReader = new BufferedReader(inputFile);
        String line;

        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);

        bufferReader.readLine();
        while ((line = bufferReader.readLine()) != null) {

            String[] parts = line.split(";");

            pStmt = con.prepareStatement("insert into  kis.admin(nutzerID) values (?)");
            for(int i = 0; i<1;i++){
                pStmt.setString(i+1,parts[i]);
            }
            pStmt.executeUpdate();

        }

        // clean up connections
        pStmt.close();
        con.close();
        //Close the buffer reader
        bufferReader.close();
    }

    private static void importUnterhaltung(String insertUnterhaltung) throws Exception{
        FileReader inputFile = new FileReader(folder + insertUnterhaltung);
        BufferedReader bufferReader = new BufferedReader(inputFile);
        String line;

        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);

        bufferReader.readLine();


        pStmt = con.prepareStatement("insert into  kis.unterhaltung values ()");
        for(int i = 0; i<4;i++){
            pStmt.executeUpdate();
        }

        // clean up connections
        pStmt.close();
        con.close();
        //Close the buffer reader
        bufferReader.close();
    }

    private static void importVeranstaltung(String insertVeranstaltung) throws Exception{
        FileReader inputFile = new FileReader(folder + insertVeranstaltung);
        BufferedReader bufferReader = new BufferedReader(inputFile);
        String line;

        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);

        bufferReader.readLine();
        while ((line = bufferReader.readLine()) != null) {

            String[] parts = line.split(";");

            pStmt = con.prepareStatement("insert into  kis.veranstaltung(vName, vDatum, vBeschreibung, vTitel, nutzerID) values (?, ?, ?, ?, ?)");
            for(int i = 0; i<5;i++){
                if(i==1){
                    DateFormat formatter = new SimpleDateFormat("yyyy-MM-dd");
                    Date myDate = formatter.parse(parts[i]);
                    pStmt.setString(i+1,parts[i]);
                }else {
                    pStmt.setString(i + 1, parts[i]);
                }
            }
            pStmt.executeUpdate();
        }

        // clean up connections
        pStmt.close();
        con.close();
        //Close the buffer reader
        bufferReader.close();
    }

    private static void importBeitrag(String insertBeitrag) throws Exception{
        FileReader inputFile = new FileReader(folder + insertBeitrag);
        BufferedReader bufferReader = new BufferedReader(inputFile);
        String line;

        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);

        bufferReader.readLine();
        while ((line = bufferReader.readLine()) != null) {

            String[] parts = line.split(";");

            pStmt = con.prepareStatement("insert into  kis.beitrag(bTitel, bInhalt, nutzerID, gruppenID)values (?, ?, ?, ?)");
            for(int i = 0; i<4;i++){
                pStmt.setString(i+1,parts[i]);
            }
            pStmt.executeUpdate();

        }

        // clean up connections
        pStmt.close();
        con.close();
        //Close the buffer reader
        bufferReader.close();
    }

    private static void importGruppe(String insertGruppe) throws Exception{
        FileReader inputFile = new FileReader(folder + insertGruppe);
        BufferedReader bufferReader = new BufferedReader(inputFile);
        String line;

        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);

        bufferReader.readLine();
        while ((line = bufferReader.readLine()) != null) {

            String[] parts = line.split(";");

            pStmt = con.prepareStatement("insert into  kis.gruppe(gTitel, gThema, nutzerID)values (?, ?, ?)");
            for(int i = 0; i<3;i++){
                pStmt.setString(i+1,parts[i]);
            }
            pStmt.executeUpdate();

        }

        // clean up connections
        pStmt.close();
        con.close();
        //Close the buffer reader
        bufferReader.close();
    }

    private static void importBenutzer(String insertBenutzer) throws Exception {
        FileReader inputFile = new FileReader(folder + insertBenutzer);
        BufferedReader bufferReader = new BufferedReader(inputFile);
        String line;

        Class.forName("com.mysql.jdbc.Driver");
        con = DriverManager.getConnection(database + "?user=" + user + "&password=" + pass);

        bufferReader.readLine();
        while ((line = bufferReader.readLine()) != null) {

            String[] parts = line.split(";");

            pStmt = con.prepareStatement("insert into  kis.benutzer(passwort, interesse, " +
                    "land,email,telNr,plz,ort,vorname,nachname,age,groesse,geschlecht," +
                    "beruf) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?, ?)");
            for(int i = 0; i<13;i++){
                pStmt.setString(i+1,parts[i]);
            }
            pStmt.executeUpdate();

        }

        // clean up connections
        pStmt.close();
        con.close();
        //Close the buffer reader
        bufferReader.close();
    }
}