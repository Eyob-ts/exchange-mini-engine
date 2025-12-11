import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

declare global {
    interface Window {
        Pusher: any;
        Echo: any;
    }
}

window.Pusher = Pusher;

// Only initialize Echo if the required environment variables are set
const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbHost = import.meta.env.VITE_REVERB_HOST;

let echo: Echo<any> | null = null;

if (reverbKey && reverbHost) {
    try {
        echo = new Echo({
            broadcaster: 'reverb',
            key: reverbKey,
            wsHost: reverbHost,
            wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
            wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        });
    } catch (error) {
        console.warn('Failed to initialize Echo:', error);
        echo = null;
    }
} else {
    console.warn('Echo (Reverb) not initialized: VITE_REVERB_APP_KEY or VITE_REVERB_HOST not set');
}

// Create a dummy echo object that won't cause errors if echo is null
const echoProxy = echo || {
    channel: () => ({
        listen: () => echoProxy,
    }),
    private: () => ({
        listen: () => echoProxy,
    }),
    join: () => echoProxy,
    leave: () => echoProxy,
    disconnect: () => {},
    socketId: () => null,
} as any;

export default echoProxy;
