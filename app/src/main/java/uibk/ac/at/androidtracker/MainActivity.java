package uibk.ac.at.androidtracker;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.os.Bundle;
import android.support.v4.content.LocalBroadcastManager;
import android.telephony.TelephonyManager;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;


public class MainActivity extends Activity {

    public static final String EXTRA_MESSAGE = "uibk.ac.at.helloworld.MESSAGE";
    private static String imei;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        IntentFilter filter = new IntentFilter(LocationUpdaterService.UPDATE_ACTION_BROADCAST);
        LocalBroadcastManager.getInstance(this).registerReceiver(new LocationReceiver(this), filter);
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

    /**
     * Called when the user clicks the Send button
     * @param view the clicked button
     */
    public void sendMessage(View view){
        Button btn = (Button) findViewById(R.id.btnSend);
        btn.setEnabled(false);
        Intent intent = new Intent(this, LocationUpdaterService.class);
        intent.setAction(LocationUpdaterService.ACTION_START_UPDATING);
        startService(intent);
    }


}
