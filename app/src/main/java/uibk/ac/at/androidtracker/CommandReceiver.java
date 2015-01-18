package uibk.ac.at.androidtracker;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

public class CommandReceiver extends BroadcastReceiver {
    private MainActivity activity;
    public CommandReceiver(MainActivity activity){
        this.activity = activity;
    }

    @Override
    public void onReceive(Context context, Intent intent) {
        if(intent.hasExtra(LocationUpdaterService.EXTRA_CMD)){
            String cmd = intent.getStringExtra(LocationUpdaterService.EXTRA_CMD);
            String data = intent.getStringExtra(LocationUpdaterService.EXTRA_DATA);
            switch(cmd){
                case "lock": sendLockDevice(data);
            }
        }
    }

    private void sendLockDevice(String data){
        activity.lockDevice(data);
    }
}