package uibk.ac.at.androidtracker;

import android.app.admin.DeviceAdminReceiver;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.widget.Toast;

public class AdminWipeLockReceiver extends DeviceAdminReceiver {
    public AdminWipeLockReceiver() {
    }

    @Override
    public void onEnabled(Context context, Intent intent){
        Toast.makeText(context, "Device Administration enabled", Toast.LENGTH_SHORT).show();
    }

    @Override
    public void onDisabled(Context context, Intent intent){
        Toast.makeText(context, "Device Administration disabled", Toast.LENGTH_SHORT).show();
    }
}
