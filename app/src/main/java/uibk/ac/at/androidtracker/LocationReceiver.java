package uibk.ac.at.androidtracker;

import android.app.Activity;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.widget.TextView;

public class LocationReceiver extends BroadcastReceiver {

    private Activity activity;
    public LocationReceiver(Activity activity){
        this.activity = activity;
    }

    /**
     * Called when location updates are received from the location service.
     * Logs the updated location in the main activity's log view
     * @param context the context from which the broadcast was sent (i.e. the location service)
     * @param intent the intent containing the broadcast information
     */
    @Override
    public void onReceive(Context context, Intent intent) {
        TextView log = (TextView) activity.findViewById(R.id.txtLog);
        if(intent.hasExtra(LocationUpdaterService.EXTRA_LOCATION)){
            log.append(intent.getStringExtra(LocationUpdaterService.EXTRA_LOCATION));
            log.append("\n");
        }
        if(intent.hasExtra(LocationUpdaterService.EXTRA_STATUS)){
            log.append(intent.getStringExtra(LocationUpdaterService.EXTRA_STATUS));
            log.append("\n");
        }
     }
}
