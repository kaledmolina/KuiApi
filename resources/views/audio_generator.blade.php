<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tone.js Note Generator</title>
    <script src="https://unpkg.com/tone"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Tone.js Note Generator</h1>

        <div class="mb-4">
            <button id="start-audio" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Start Audio
                Context</button>
            <span id="audio-status" class="ml-2 text-gray-500">Audio not started</span>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Generate Notes (C3 - C4)</h2>
            <button id="generate-all"
                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 disabled:opacity-50"
                disabled>Generate & Upload All</button>
        </div>

        <div id="log" class="bg-gray-900 text-green-400 p-4 rounded h-64 overflow-y-auto font-mono text-sm">
            <div>Logs will appear here...</div>
        </div>
    </div>

    <script>
        const noteNames = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        const notes = [];

        for (let i = 0; i < 88; i++) {
            // El piano empieza en A0 (índice 9 de la escala de C)
            const noteName = noteNames[(i + 9) % 12];
            const octave = Math.floor((i + 9) / 12);
            notes.push({ note: noteName, octave: octave });
        }

        const logDiv = document.getElementById('log');
        const startBtn = document.getElementById('start-audio');
        const generateBtn = document.getElementById('generate-all');
        const audioStatus = document.getElementById('audio-status');

        function log(message) {
            const div = document.createElement('div');
            div.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            logDiv.prepend(div);
        }

        startBtn.addEventListener('click', async () => {
            await Tone.start();
            audioStatus.textContent = "Audio Context Started";
            audioStatus.classList.remove('text-gray-500');
            audioStatus.classList.add('text-green-500');
            generateBtn.disabled = false;
            log("Audio Context started");
        });

        generateBtn.addEventListener('click', async () => {
            generateBtn.disabled = true;
            log("Starting generation sequence...");

            for (const n of notes) {
                await generateAndUploadNote(n.note, n.octave);
                // Add a small delay between notes to ensure clean recording
                await new Promise(r => setTimeout(r, 500));
            }

            log("All notes processed!");
            generateBtn.disabled = false;
        });

        async function generateAndUploadNote(noteName, octave) {
            const fullName = `${noteName}${octave}`;
            log(`Generating ${fullName}...`);

            const dest = Tone.context.createMediaStreamDestination();
            const recorder = new MediaRecorder(dest.stream);

            // 1. Soft Piano / Rhodes Electric Piano Synth Parameters
            const synth = new Tone.FMSynth({
                harmonicity: 1, // Ratio of modulator to carrier
                modulationIndex: 1.5,
                oscillator: {
                    type: "sine" // Pure base tone
                },
                envelope: {
                    attack: 0.01, // Fast strike
                    decay: 0.2,   // Initial drop
                    sustain: 0.2, // Level while held
                    release: 2.0  // Long, soft fade out
                },
                modulation: {
                    type: "square" // Adds some overtone texture
                },
                modulationEnvelope: {
                    attack: 0.01,
                    decay: 0.1,
                    sustain: 0,
                    release: 0
                }
            });

            // 2. Lowpass Filter to cut harsh high frequencies
            const filter = new Tone.Filter({
                type: "lowpass",
                frequency: 1200, // Very soft cutoff
                rolloff: -24
            });

            // Reverb can also help soften it, but let's stick to the Filter for pure notes first.
            synth.connect(filter);
            filter.connect(dest);

            // Connect to master to hear it locally
            // filter.toDestination(); 

            return new Promise((resolve) => {
                const chunks = [];
                recorder.ondataavailable = evt => chunks.push(evt.data);
                recorder.onstop = async () => {
                    const blob = new Blob(chunks, { type: 'audio/webm' });
                    await uploadNote(noteName, octave, blob);
                    resolve();
                };

                recorder.start();
                // Play note softly with a bit of velocity if available, otherwise default
                synth.triggerAttackRelease(fullName, "2n");

                // Stop recording after the note + long release tail
                setTimeout(() => {
                    recorder.stop();
                }, 2500); // 2.5s covers the 2.0s release tail beautifully
            });
        }

        async function uploadNote(noteName, octave, blob) {
            const formData = new FormData();
            formData.append('note_name', noteName);
            formData.append('octave', octave);
            formData.append('audio', blob, `${noteName}${octave}.webm`);

            try {
                const response = await fetch('/audio-generator/upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                if (data.success) {
                    log(`Uploaded ${noteName}${octave}: ${data.path}`);
                } else {
                    log(`Error uploading ${noteName}${octave}`);
                }
            } catch (error) {
                log(`Network error uploading ${noteName}${octave}: ${error}`);
            }
        }
    </script>
</body>

</html>