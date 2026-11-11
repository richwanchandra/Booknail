{{-- resources/views/components/fcm-scripts.blade.php --}}
{{-- Inject Firebase config as <meta> tags (no secrets exposed here) --}}
{{-- and dynamically load the FCM init script only for authenticated users --}}
@if(auth()->check())
<meta name="user-id" content="{{ auth()->id() }}">
<meta name="fcm-api-key"            content="{{ config('services.firebase.api_key', '') }}">
<meta name="fcm-auth-domain"        content="{{ config('services.firebase.auth_domain', '') }}">
<meta name="fcm-project-id"         content="{{ config('services.firebase.project_id', '') }}">
<meta name="fcm-storage-bucket"     content="{{ config('services.firebase.storage_bucket', '') }}">
<meta name="fcm-messaging-sender-id"content="{{ config('services.firebase.messaging_sender_id', '') }}">
<meta name="fcm-app-id"             content="{{ config('services.firebase.app_id', '') }}">
@if(config('services.firebase.vapid_key'))
<meta name="fcm-vapid-key"          content="{{ config('services.firebase.vapid_key', '') }}">
@endif
<script type="module" src="{{ asset('js/fcm-init.js') }}"></script>
@endif