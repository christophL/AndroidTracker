package uibk.ac.at.androidtracker;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

public class CommandReceiver extends BroadcastReceiver {
    private MainActivity activity;
    public CommandReceiver(MainActivity activity){
        this.activity = activity;
    }

    /**
     * Called when commands are received from the server.
     * Calls the wipe/lock methods of the main activity for the matching received command
     * @param context the context from which the broadcast was sent (i.e. the location service)
     * @param intent the intent containing the broadcast information
     */
    @Override
    public void onReceive(Context context, Intent intent) {
        if(intent.hasExtra(PostLocationTask.EXTRA_CMD)){
            String cmd = intent.getStringExtra(PostLocationTask.EXTRA_CMD);
            String data = intent.getStringExtra(PostLocationTask.EXTRA_DATA);
            switch(cmd){
                case "lock": sendLockDevice(data); break;
                case "wipe": sendWipeDevice(); break;
            }
        }
    }

    /**
     * Locks the device, setting the password to the provided data
     * @param data the password to be set
     */
    private void sendLockDevice(String data){
        activity.lockDevice(data);
    }

    /**
     * Performs a factory-reset of the device
     */
    private void sendWipeDevice() { activity.wipeDevice(); }
}
