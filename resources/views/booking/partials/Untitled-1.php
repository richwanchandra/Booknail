namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationController extends Controller
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function sendBookingNotification($deviceToken)
    {
        // Compose the push notification message
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create(
                'Nail Art Booking Confirmed!',
                'Your upcoming appointment is set. See you soon!'
            ))
            ->withData([
                'booking_id' => '12345',
                'click_action' => 'OPEN_BOOKINGS_PAGE'
            ]);

        try {
            // Send the notification via FCM
            $this->messaging->send($message);
            return response()->json(['success' => true, 'message' => 'Notification sent!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
