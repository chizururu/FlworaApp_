<?php

namespace App\Console\Commands;

use App\Events\DeviceStatusEvent;
use App\Events\SensorDataEvent;
use App\Models\Device;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use PhpMqtt\Client\Exceptions\ConfigurationInvalidException;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;
use PhpMqtt\Client\Exceptions\ConnectionNotAvailableException;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\InvalidMessageException;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;
use PhpMqtt\Client\Exceptions\ProtocolViolationException;
use PhpMqtt\Client\Exceptions\RepositoryException;
use PhpMqtt\Client\Facades\MQTT;

class MqttReceiver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt-receiver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //
        try {
            $base = env('MQTT_CLIENT_ID', 'laravel-subscriber');
            $clientId = $base . '-' . gethostname() . '-' . getmypid();

            config(['mqtt.connections.default.client_id' => $clientId]);
            config(['mqtt-client.connections.default.client_id' => $clientId]);

            $this->info("MQTT client id: $clientId");

            $mqtt = MQTT::connection('default');

            /// Subscribe
            try {
                /// 1. Ambil data sensor yang dikirimkan dari ESP32
                $mqtt->subscribe('device/+/sensor-data', function ($topic, $message) {
                    $data = json_decode($message, true);

                    $device = Device::find($data['device_id']);

                    /// Panggil broadcast untuk mendapatkan data sensor dari ESP32 ke Flutter secara realtime
                    broadcast(new SensorDataEvent($device->id, [
                            $data['soil_moisture'],
                            $data['temperature'],
                            $data['humidity']
                        ]
                    ));
                }, 0);

                /// 2. Status perangkat
                $mqtt->subscribe('device/+/status', function ($topic, $message) {
                    $this->line("[$topic] => $message (retained mungkin)");

                    $parts = explode('/', $topic);
                    $deviceId = $parts[1] ?? null;

                    $this->line("Device found: " . $deviceId);

                    if ($deviceId) {
                        $status = json_decode($message, true);
                        $isOnline = $status['status'] === 'online';

                        $device = Device::find($deviceId);

                        $this->line("Device found: " . ($device ? $device->id . ' - ' . $device->name : 'NOT FOUND'));

                        // Update DB status
                        $device->update(['is_online' => $isOnline, 'last_seen' => now()]);

                        // Broadcast ke Reverb/Flutter
                        broadcast(new DeviceStatusEvent($deviceId, $isOnline));
                    }
                }, 1);

                $mqtt->loop();

            } catch (DataTransferException|RepositoryException $e) {
                /// Kosongkan
            } catch (InvalidMessageException|ProtocolViolationException|MqttClientException $e) {

            }
        } catch (BindingResolutionException|ConfigurationInvalidException|ConnectingToBrokerFailedException|ConnectionNotAvailableException|ProtocolNotSupportedException $e) {
            $this->info('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
