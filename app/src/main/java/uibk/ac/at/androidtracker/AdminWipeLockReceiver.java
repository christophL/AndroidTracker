package uibk.ac.at.androidtracker;

import android.app.admin.DeviceAdminReceiver;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.widget.Toast;

public class AdminWipeLockReceiver extends DeviceAdminReceiver {
    public AdminWipeLockReceiver() {
    }

    /**
     * Called when device administration was activated.
     * Simpy shows a message to show the user that the operation was successful
     * @param context (i.e. main activity)
     * @param intent additional information
     */
    @Override
    public void onEnabled(Context context, Intent intent){
        Toast.makeText(context, "Device Administration enabled", Toast.LENGTH_SHORT).show();
    }

    /**
     * Called when device administration was de-activated.
     * Simpy shows a message to show the user that the operation was successful
     * @param context (i.e. main activity)
     * @param intent additional information
     */
    @Override
    public void onDisabled(Context context, Intent intent){
        Toast.makeText(context, "Device Administration disabled", Toast.LENGTH_SHORT).show();
    }
}
