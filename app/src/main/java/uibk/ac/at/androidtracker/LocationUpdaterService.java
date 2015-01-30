package uibk.ac.at.androidtracker;

import android.app.Activity;
import android.app.IntentService;
import android.content.Context;
import android.content.Intent;
import android.content.IntentSender;
import android.location.Location;
import android.os.Bundle;
import android.support.v4.content.LocalBroadcastManager;
import android.telephony.TelephonyManager;

import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.GooglePlayServicesClient;
import com.google.android.gms.common.GooglePlayServicesUtil;
import com.google.android.gms.location.LocationClient;
import com.google.android.gms.location.LocationListener;
import com.google.android.gms.location.LocationRequest;

public class LocationUpdaterService
        extends IntentService
        implements GooglePlayServicesClient.ConnectionCallbacks,
            GooglePlayServicesClient.OnConnectionFailedListener, LocationListener {
    public static final String ACTION_START_UPDATING = "uibk.ac.at.androidtracker.action.START_UPDATING";

    public static final String BROADCAST_LOCATION_UPDATE = "uibk.ac.at.androidtracker.LOCATION_UPDATE";

    public static final String EXTRA_UPDATE_INTERVAL = "uibk.ac.at.androidtracker.UPDATEINTERVAL";
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
        /*
         * the locationUpdaterService itself listens for connection/connection failed events and
         * location updates
         */
        client = new LocationClient(this, this, this);
        loadDeviceId();
    }

    /**
     * Called when an Intent was received by the service (i.e. a "start updating" intent)
     * Connects the location client to Google Play Services.
     * @param intent the intent to be handled
     */
    @Override
    protected void onHandleIntent(Intent intent) {
        if(intent == null) return;
        final String action = intent.getAction();

        if (ACTION_START_UPDATING.equals(action)) {
            updateInterval = intent.getIntExtra(EXTRA_UPDATE_INTERVAL, 60);
            if(servicesAvailable()){
                client.connect();
            }
        }
    }

    /**
     * Called when Google Play services are connected to the location client.
     * Sets the location client to provide location updates periodically
     * @param bundle
     */
    @Override
    public void onConnected(Bundle bundle) {
        LocationRequest locRequest = LocationRequest.create();
        locRequest.setInterval(1000 * updateInterval);
        locRequest.setFastestInterval(2000);
        locRequest.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);
        client.requestLocationUpdates(locRequest, this);
        sendUpdateBroadcast(EXTRA_STATUS, "Connected to Play Services.");
    }

    /**
     * Called when Google Play Services were disconnected from the location client.
     * Logs this information.
     */
    @Override
    public void onDisconnected() {
        sendUpdateBroadcast(EXTRA_STATUS, "Disconnected from Play Services.");
    }

    /**
     * Called when Google Play services could not be connected to the location client.
     * Logs the error in the main activities log control
     * @param connectionResult the result of the connection attempt, containing further information
     */
    @Override
    public void onConnectionFailed(ConnectionResult connectionResult) {
        sendUpdateBroadcast(EXTRA_STATUS, "Connection failed");
        if (connectionResult.hasResolution()) {
            try {
                // Start an Activity that tries to resolve the error
                connectionResult.startResolutionForResult(new Activity(), CONNECTION_FAILURE_RESOLUTION_REQUEST);
            } catch (IntentSender.SendIntentException e) {
                e.printStackTrace();
            }
        } else {
            sendUpdateBroadcast(EXTRA_STATUS, String.valueOf(connectionResult.getErrorCode()));
        }
    }

    /**
     * Sends a local broadcast to notify the main activity that some updates are available
     * (e.g.: new location data, or errors -- mainly for logging)
     */
    private void sendUpdateBroadcast(String type, String message){
        Intent updateIntent = new Intent(BROADCAST_LOCATION_UPDATE);
        updateIntent.putExtra(type, message);
        LocalBroadcastManager.getInstance(this).sendBroadcast(updateIntent);
    }


    /**
     * Checks whether Google Play services is available (needed for the location API)
     * @return true iff Google play services is available
     */
    private boolean servicesAvailable() {
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

    /**
     * Called when the location client determined a new location.
     * Sends the received location data to the server
     * @param location the location returned from the location client
     */
    @Override
    public void onLocationChanged(Location location) {
        String latitude = String.valueOf(location.getLatitude());
        String longitude = String.valueOf(location.getLongitude());
        String locationString = "Location Update: " + latitude
                + ", " + longitude;
        String accuracy = String.valueOf(location.getAccuracy());
        sendUpdateBroadcast(EXTRA_LOCATION, locationString);
        new PostLocationTask(this).execute(imei, latitude, longitude, accuracy);
    }

    /**
     * Loads the device's IMEI needed by the server to identify received location data
     */
    private void loadDeviceId(){
        if(imei != null) return;
        TelephonyManager tmgr = (TelephonyManager)getSystemService(Context.TELEPHONY_SERVICE);
        imei = tmgr.getDeviceId();
    }
}
