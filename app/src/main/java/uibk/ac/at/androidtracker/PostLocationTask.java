package uibk.ac.at.androidtracker;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.support.v4.content.LocalBroadcastManager;
import android.util.Pair;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.net.URL;
import java.net.URLEncoder;
import java.security.KeyManagementException;
import java.security.KeyStore;
import java.security.KeyStoreException;
import java.security.NoSuchAlgorithmException;
import java.security.cert.Certificate;
import java.security.cert.CertificateException;
import java.security.cert.CertificateFactory;
import java.util.ArrayList;
import java.util.List;

import javax.net.ssl.HostnameVerifier;
import javax.net.ssl.HttpsURLConnection;
import javax.net.ssl.SSLContext;
import javax.net.ssl.SSLSession;
import javax.net.ssl.TrustManagerFactory;

public class PostLocationTask extends AsyncTask<String, Void, Pair<String, String>> {
    public static final String BROADCAST_CMD_RECEIVED = "uibk.ac.at.androidtracker.CMD_RECEIVED";
    public static final String EXTRA_CMD = "uibk.ac.at.androidtracker.EXTRA_CMD";
    public static final String EXTRA_DATA = "uibk.ac.at.androidtracker.EXTRA_DATA";

    /**
     * The context (i.e. activity) which called the task - needed to send the broadcast intent
     * and open the raw resource containing our self-signed certificate
     */
    private Context ctx;
    private static SSLContext sslCtx;

    public PostLocationTask(Context ctx){
        super();
        this.ctx = ctx;
    }

    /**
     * Creates a HTTPS connection to the server using the customized SSL context
     * (see initSsl())
     * @return the HTTPS connection object
     */
    private HttpsURLConnection createConnection(){
        if(sslCtx == null){
            initSsl();
        }
        HttpsURLConnection conn = null;
        try {
            URL servUrl = new URL("https://192.168.43.194/infsecApp/store.php");
            conn = (HttpsURLConnection) servUrl.openConnection();
            conn.setSSLSocketFactory(sslCtx.getSocketFactory());
            //no need to do verification, we only trust our own certificate anyways
            conn.setHostnameVerifier(new HostnameVerifier() {
                @Override
                public boolean verify(String hostname, SSLSession session) {
                    return true;
                }
            });
            conn.setReadTimeout(10000);
            conn.setConnectTimeout(15000);
            conn.setRequestMethod("POST");
            conn.setDoInput(true);
            conn.setDoOutput(true);
        } catch (IOException e) {
            e.printStackTrace();
        }
        return conn;
    }

    /**
     * Sets up the SSL context to trust our self-signed server certificate (and nothing else)
     * The context is only set-up once and reused later
     */
    private void initSsl(){
        String keyStoreType = KeyStore.getDefaultType();
        String tmfAlgorithm = TrustManagerFactory.getDefaultAlgorithm();
        try {
            CertificateFactory cf = CertificateFactory.getInstance("X.509");
            InputStream caInput =  new BufferedInputStream(ctx.getResources().openRawResource(R.raw.server));
            Certificate cert = cf.generateCertificate(caInput);
            caInput.close();

            KeyStore store = KeyStore.getInstance(keyStoreType);
            store.load(null, null);
            store.setCertificateEntry("server", cert);

            TrustManagerFactory tmf = TrustManagerFactory.getInstance(tmfAlgorithm);
            tmf.init(store);

            sslCtx = SSLContext.getInstance("TLS");
            sslCtx.init(null, tmf.getTrustManagers(), null);
        } catch (CertificateException | IOException | KeyStoreException | NoSuchAlgorithmException | KeyManagementException e) {
            e.printStackTrace();
        }
    }

    /**
     * Transforms the provided parameters into a parameter string for HTTP-POSTS
     * (param1=value1&param2=value2&...)
     * @param params the parameters to be encoded into the string
     * @return the POST parameter string
     */
    private String makeQuery(List<NameValuePair> params){
        StringBuilder res = new StringBuilder();
        boolean isFirst = true;

        for(NameValuePair p : params){
            if(isFirst) isFirst = false;
            else res.append('&');

            try {
                res.append(URLEncoder.encode(p.getName(), "UTF-8"));
                res.append('=');
                res.append(URLEncoder.encode(p.getValue(), "UTF-8"));
            } catch (UnsupportedEncodingException e) {
                e.printStackTrace();
            }
        }
        return res.toString();
    }

    /**
     * Parses the JSON-encode server response.
     * Expected responses are of the following form:
     * { "200": { "cmd": "aCommand", "data": "dataRequiredForTheCommand" } }
     * @param response the JSON-encoded server response
     * @return the command string and provided data (or null if the response could not be parsed)
     */
    private Pair<String, String> parseJsonResponse(String response){
        try {
            JSONObject obj = new JSONObject(response);
            if(obj.has("200")){
                obj = obj.getJSONObject("200");
            }
            if(obj.has("cmd")){
                String cmd = obj.getString("cmd");
                String data = obj.getString("data");
                return new Pair<>(cmd, data);
            }
        } catch (JSONException e) {
            e.printStackTrace();
        }
        return null;
    }

    /**
     * Performs the HTTP-POST in a background thread.
     * (performing it on the main thread is not allowed by android and will lead to an exception)
     * POSTs the updated location data and returns the command/data sent in the response (if any)
     * @param params parameters (IMEI, latitude, longitude, accuracy)
     * @return the command/data received in response (or null if no command was received)
     */
    @Override
    protected Pair<String, String> doInBackground(String... params) {
        String imei = params[0];
        String latitude = params[1];
        String longitude = params[2];
        String accuracy = params[3];

        HttpsURLConnection conn = null;
        try {
            conn = createConnection();
            List<NameValuePair> postParams = new ArrayList<>(3);
            postParams.add(new BasicNameValuePair("IMEI", imei));
            postParams.add(new BasicNameValuePair("LAT", latitude));
            postParams.add(new BasicNameValuePair("LONG", longitude));
            postParams.add(new BasicNameValuePair("ACC", accuracy));

            BufferedWriter writer = new BufferedWriter(new OutputStreamWriter(conn.getOutputStream(), "UTF-8"));
            writer.write(makeQuery(postParams));
            writer.close();

            BufferedReader reader = new BufferedReader(new InputStreamReader(conn.getInputStream()));
            String line, response = "";
            while((line = reader.readLine()) != null){
                response += line;
            }
            reader.close();
            System.out.println("Response: " + response);
            if(response.length() != 0) return parseJsonResponse(response);
        } catch (IOException e) {
            e.printStackTrace();
        } finally {
            if (conn != null) {
                conn.disconnect();
            }
        }
        return null;
    }

    /**
     * Called after the POST-request was completed.
     * If a command was received from the server, the command will be forwarded to the
     * CommandReceiver using a local broadcast
     * @param result the command/data returned by the server (possibly none)
     */
    @Override
    protected void onPostExecute(Pair<String, String> result){
        if(result != null){
            sendCmdBroadcast(result.first, result.second);
        }
    }

    /**
     * Sends the local command broadcast
     * @param cmd the received command
     * @param data the received data
     */
    private void sendCmdBroadcast(String cmd, String data){
        Intent cmdIntent = new Intent(BROADCAST_CMD_RECEIVED);
        cmdIntent.putExtra(EXTRA_CMD, cmd);
        cmdIntent.putExtra(EXTRA_DATA, data);
        LocalBroadcastManager.getInstance(ctx).sendBroadcast(cmdIntent);
    }
}
