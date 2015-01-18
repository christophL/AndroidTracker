package uibk.ac.at.androidtracker;

import android.app.Activity;
import android.app.IntentService;
import android.content.Context;
import android.content.Intent;
import android.content.IntentSender;
import android.location.Location;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.content.LocalBroadcastManager;
import android.telephony.TelephonyManager;
import android.util.Pair;

import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.GooglePlayServicesClient;
import com.google.android.gms.common.GooglePlayServicesUtil;
import com.google.android.gms.location.LocationClient;
import com.google.android.gms.location.LocationListener;
import com.google.android.gms.location.LocationRequest;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.util.EntityUtils;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

public class LocationUpdaterService
        extends IntentService
        implements GooglePlayServicesClient.ConnectionCallbacks,
            GooglePlayServicesClient.OnConnectionFailedListener, LocationListener {
    public static final String ACTION_START_UPDATING = "uibk.ac.at.androidtracker.action.START_UPDATING";

    public static final String BROADCAST_CMD_RECEIVED = "uibk.ac.at.androidtracker.CMD_RECEIVED";
    public static final String BROADCAST_LOCATION_UPDATE = "uibk.ac.at.androidtracker.LOCATION_UPDATE";


    public static final String EXTRA_UPDATE_INTERVAL = "uibk.ac.at.androidtracker.UPDATEINTERVAL";
    public static final String EXTRA_CMD = "uibk.ac.at.androidtracker.EXTRA_CMD";
    public static final String EXTRA_DATA = "uibk.ac.at.androidtracker.EXTRA_DATA";
    public static final String EXTRA_LOCATION = "uibk.ac.at.androidtracker.EXTRA_LOCATION";
    public static final String EXTRA_STATUS = "uibk.ac.at.androidtracker.EXTRA_STATUS";

    private final static int CONNECTION_FAILURE_RESOLUTION_REQUEST = 9000;
    private static String imei = null;

    private LocationClient client;
    private int updateInterval;

    public LocationUpdaterService() {
        super("LocationUpdaterService");
    }

    public void onCreate(){
        super.onCreate();
        client = new LocationClient(this, this, this);
        loadDeviceId();
    }

    @Override
    protected void onHandleIntent(Intent intent) {
        if (intent != null) {
            final String action = intent.getAction();

            if (ACTION_START_UPDATING.equals(action)) {
                updateInterval = intent.getIntExtra(EXTRA_UPDATE_INTERVAL, 60);
                handleActionStartUpdating();
            }
        }
    }

    private void handleActionStartUpdating() {
        if(servicesConnected()){
            client.connect();
        }
    }

    @Override
    public void onConnected(Bundle bundle) {
        LocationRequest locRequest = LocationRequest.create();
        locRequest.setInterval(1000 * updateInterval);
        locRequest.setFastestInterval(2000);
        locRequest.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);
        client.requestLocationUpdates(locRequest, this);
        sendUpdateBroadcast(EXTRA_STATUS, "Connected to Play Services.");
    }

    @Override
    public void onDisconnected() {
        sendUpdateBroadcast(EXTRA_STATUS, "Disconnected from Play Services.");
    }

    @Override
    public void onConnectionFailed(ConnectionResult connectionResult) {
        sendUpdateBroadcast(EXTRA_STATUS, "Connection failed");
        if (connectionResult.hasResolution()) {
            try {
                // Start an Activity that tries to resolve the error
                connectionResult.startResolutionForResult(new Activity(), CONNECTION_FAILURE_RESOLUTION_REQUEST);
            } catch (IntentSender.SendIntentException e) {
                // Log the error
                e.printStackTrace();
            }
        } else {
            sendUpdateBroadcast(EXTRA_STATUS, String.valueOf(connectionResult.getErrorCode()));
        }
    }

    private void sendUpdateBroadcast(String type, String message){
        Intent updateIntent = new Intent(BROADCAST_LOCATION_UPDATE);
        updateIntent.putExtra(type, message);
        LocalBroadcastManager.getInstance(this).sendBroadcast(updateIntent);
    }


    private boolean servicesConnected() {
        int resultCode = GooglePlayServicesUtil.isGooglePlayServicesAvailable(this);
        if (ConnectionResult.SUCCESS == resultCode) {
            return true;
        } else {
            String errorString = GooglePlayServicesUtil.getErrorString(resultCode);
            if (errorString != null) {
                sendUpdateBroadcast(EXTRA_STATUS, errorString);
            }
            return false;
        }
    }

    @Override
    public void onLocationChanged(Location location) {
        System.out.println("in location Changed");
        String latitude = String.valueOf(location.getLatitude());
        String longitude = String.valueOf(location.getLongitude());
        String locationString = "Location Update: " + latitude
                + ", " + longitude;
        sendUpdateBroadcast(EXTRA_LOCATION, locationString);
        new PostLocationTask().execute(imei, latitude, longitude);
    }

    private void loadDeviceId(){
        if(imei != null) return;
        TelephonyManager tmgr = (TelephonyManager)getSystemService(Context.TELEPHONY_SERVICE);
        imei = tmgr.getDeviceId();
    }

    private class PostLocationTask extends AsyncTask<String, Void, Pair<String, String>> {
        @Override
        protected Pair<String, String> doInBackground(String... params) {
            String imei = params[0];
            String latitude = params[1];
            String longitude = params[2];

            HttpClient httpclient = new DefaultHttpClient();
            HttpPost httppost = new HttpPost("http://192.168.1.101/infsecApp/store.php");

            try {
                List<NameValuePair> nameValuePairs = new ArrayList<>(3);
                nameValuePairs.add(new BasicNameValuePair("IMEI", imei));
                nameValuePairs.add(new BasicNameValuePair("LAT", latitude));
                nameValuePairs.add(new BasicNameValuePair("LONG", longitude));
                httppost.setEntity(new UrlEncodedFormEntity(nameValuePairs));

                // Execute HTTP Post Request
                HttpResponse response = httpclient.execute(httppost);
                HttpEntity entity = response.getEntity();
                String responseString = EntityUtils.toString(entity);

                JSONObject obj = new JSONObject(responseString);
                if(obj.has("cmd")){
                    String cmd = obj.getString("cmd");
                    String data = obj.getString("data");
                    return new Pair<>(cmd, data);
                }
            } catch (IOException | JSONException e) {
                e.printStackTrace();
            }
            return null;
        }

        @Override
        protected void onPostExecute(Pair<String, String> result){
            if(result != null){
                sendCmdBroadcast(result.first, result.second);
            }
        }

        private void sendCmdBroadcast(String cmd, String data){
            Intent cmdIntent = new Intent(BROADCAST_CMD_RECEIVED);
            cmdIntent.putExtra(EXTRA_CMD, cmd);
            cmdIntent.putExtra(EXTRA_DATA, data);
            LocalBroadcastManager.getInstance(LocationUpdaterService.this).sendBroadcast(cmdIntent);
        }
    }
}
