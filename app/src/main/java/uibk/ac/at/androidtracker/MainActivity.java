package uibk.ac.at.androidtracker;

import android.app.Activity;
import android.app.admin.DevicePolicyManager;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.os.Bundle;
import android.support.v4.content.LocalBroadcastManager;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;


public class MainActivity extends Activity{
    private static final int REQUEST_CODE_ENABLE_ADMIN = 1;
    private boolean updatesActive;

    private DevicePolicyManager dpm;
    private ComponentName receiverName;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        IntentFilter filter = new IntentFilter(LocationUpdaterService.UPDATE_ACTION_BROADCAST);
        LocalBroadcastManager.getInstance(this).registerReceiver(new LocationReceiver(this), filter);

        CheckBox cbAdmin = (CheckBox) findViewById(R.id.cbAdmin);
        Button btnLock = (Button) findViewById(R.id.btnLock);
        dpm = (DevicePolicyManager) getSystemService(Context.DEVICE_POLICY_SERVICE);
        receiverName = new ComponentName(this, AdminWipeLockReceiver.class);

        if(savedInstanceState != null){
            TextView log = (TextView) findViewById(R.id.txtLog);
            log.setText(savedInstanceState.getCharSequence("curLog"));

            updatesActive = savedInstanceState.getBoolean("updatesActive");
            enableControls(!updatesActive);
        }
        boolean adminActive = isAdminActive();
        cbAdmin.setChecked(adminActive);
        btnLock.setEnabled(adminActive);
    }

    private boolean isAdminActive(){
        return dpm.isAdminActive(receiverName);
    }

    private void enableControls(boolean enable){
        Button btnUpdates = (Button) findViewById(R.id.btnSend);
        EditText txtInterval = (EditText) findViewById(R.id.txtUpdateInterval);
        btnUpdates.setEnabled(enable);
        txtInterval.setEnabled(enable);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();
        if (id == R.id.action_settings) {
            return true;
        }
        return super.onOptionsItemSelected(item);
    }

    @Override
    public void onSaveInstanceState(Bundle outBundle){
        super.onSaveInstanceState(outBundle);
        TextView log = (TextView) findViewById(R.id.txtLog);
        outBundle.putCharSequence("curLog", log.getText());
        outBundle.putBoolean("updatesActive", updatesActive);
    }

    /**
     * Called when the user clicks the Send button
     * @param view the clicked button
     */
    public void sendMessage(View view){
        EditText txtInterval = (EditText) findViewById(R.id.txtUpdateInterval);
        int interval;
        try{
            int parsed = Integer.parseInt(txtInterval.getText().toString());
            if(parsed < 1){
                Toast.makeText(this, "Update interval needs to be >= 1", Toast.LENGTH_LONG).show();
                return;
            }
            interval = parsed;
        } catch(NumberFormatException e) {
            Toast.makeText(this, "Could not parse provided update interval", Toast.LENGTH_LONG).show();
            return;
        }

        updatesActive = true;
        enableControls(false);
        Intent intent = new Intent(this, LocationUpdaterService.class);
        intent.setAction(LocationUpdaterService.ACTION_START_UPDATING);
        intent.putExtra(LocationUpdaterService.EXTRA_UPDATE_INTERVAL, interval);
        startService(intent);
    }

    public void onBtnLockClick(View view){
        dpm.resetPassword("secret", 0);
        dpm.lockNow();
    }

    public void onCbAdminClicked(View view) {
        boolean isChecked = ((CheckBox) view).isChecked();
        if(isChecked){
            Intent intent = new Intent(DevicePolicyManager.ACTION_ADD_DEVICE_ADMIN);
            intent.putExtra(DevicePolicyManager.EXTRA_DEVICE_ADMIN, receiverName);
            intent.putExtra(DevicePolicyManager.EXTRA_ADD_EXPLANATION, R.string.admin_explanation);
            startActivityForResult(intent, REQUEST_CODE_ENABLE_ADMIN);
        } else {
            dpm.removeActiveAdmin(receiverName);
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data){
        if(requestCode != REQUEST_CODE_ENABLE_ADMIN) return;

        CheckBox cbAdmin = (CheckBox) findViewById(R.id.cbAdmin);
        if(resultCode == Activity.RESULT_OK){
            cbAdmin.setChecked(true);
        } else {
            cbAdmin.setChecked(false);
        }
    }
}
