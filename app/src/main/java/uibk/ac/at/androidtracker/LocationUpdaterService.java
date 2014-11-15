package uibk.ac.at.androidtracker;

import android.app.Activity;
import android.app.IntentService;
import android.content.Intent;
import android.content.IntentSender;
import android.location.Location;
import android.os.Bundle;
import android.support.v4.content.LocalBroadcastManager;

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
    public static final String UPDATE_ACTION_BROADCAST = "uibk.ac.at.helloworld.UPDATE";
    public static final String UPDATE_DATA_STATUS = "uibk.ac.at.helloworld.STATUS";
    public static final String UPDATE_DATA_LOCATION = "uibk.ac.at.helloworld.LOCATION";
    private final static int CONNECTION_FAILURE_RESOLUTION_REQUEST = 9000;

    public static final String ACTION_START_UPDATING = "uibk.ac.at.helloworld.action.START_UPDATING";

    private LocationClient client;
    private LocationRequest locRequest;

    public LocationUpdaterService() {
        super("LocationUpdaterService");
    }

    public void onCreate(){
        super.onCreate();
        client = new LocationClient(this, this, this);
    }

    @Override
    protected void onHandleIntent(Intent intent) {
        if (intent != null) {
            final String action = intent.getAction();

            if (ACTION_START_UPDATING.equals(action)) {
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
        locRequest = LocationRequest.create();
        locRequest.setInterval(1000*60);
        locRequest.setFastestInterval(2000);
        locRequest.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);
        client.requestLocationUpdates(locRequest, this);
        sendUpdateBroadcast(UPDATE_DATA_STATUS, "Connected to Play Services.");
    }

    @Override
    public void onDisconnected() {
        sendUpdateBroadcast(UPDATE_DATA_STATUS, "Disconnected from Play Services.");
    }

    @Override
    public void onConnectionFailed(ConnectionResult connectionResult) {
        sendUpdateBroadcast(UPDATE_DATA_STATUS, "Connection failed");
        if (connectionResult.hasResolution()) {
            try {
                // Start an Activity that tries to resolve the error
                connectionResult.startResolutionForResult(new Activity(), CONNECTION_FAILURE_RESOLUTION_REQUEST);
            } catch (IntentSender.SendIntentException e) {
                // Log the error
                e.printStackTrace();
            }
        } else {
            sendUpdateBroadcast(UPDATE_DATA_STATUS, String.valueOf(connectionResult.getErrorCode()));
        }
    }

    private void sendUpdateBroadcast(String type, String message){
        Intent updateIntent = new Intent(UPDATE_ACTION_BROADCAST);
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
                sendUpdateBroadcast(UPDATE_DATA_STATUS, errorString);
            }
            return false;
        }
    }

    @Override
    public void onLocationChanged(Location location) {
        System.out.println("in location Changed");
        String locationString = "Location Update: " + String.valueOf(location.getLatitude())
                + ", " + String.valueOf(location.getLongitude());
        sendUpdateBroadcast(UPDATE_DATA_LOCATION, locationString);
    }
}
