<x-filament-panels::page>
    <div class="p-6 bg-white rounded-lg shadow">
        <script src="https://unpkg.com/tone"></script>

        <div class="mb-4">
            <x-filament::button id="start-audio" type="button">Start Audio Context</x-filament::button>
            <span id="audio-status" class="ml-2 text-gray-500">Audio not started</span>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-bold mb-2">Generate Notes (A0 - C8)</h2>
            <x-filament::button id="generate-all" type="button" disabled color="success">Generate & Upload All (88 Keys)</x-filament::button>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-bold mb-2">Test Notes (Visual Piano)</h2>
            <div id="piano-container" class="flex overflow-x-auto pb-4 pt-2 relative h-48 select-none">
                <!-- Keys will be generated here -->
            </div>
        </div>

        <div id="log" class="bg-gray-900 text-green-400 p-4 rounded h-64 overflow-y-auto font-mono text-sm border border-gray-700">
            <div>Logs will appear here...</div>
        </div>

        <style>
            .white-key {
                width: 40px;
                height: 160px;
                background: white;
                border: 1px solid #ccc;
                border-radius: 0 0 4px 4px;
                flex-shrink: 0;
                position: relative;
                z-index: 1;
                cursor: pointer;
                display: flex;
                align-items: flex-end;
                justify-content: center;
                padding-bottom: 8px;
                font-size: 10px;
                color: #888;
                transition: background 0.1s;
            }
            .white-key:active, .white-key.active {
                background: #eee;
                border-bottom: 4px solid #ddd;
            }
            .black-key {
                width: 24px;
                height: 100px;
                background: black;
                border: 1px solid black;
                border-radius: 0 0 4px 4px;
                position: absolute;
                z-index: 2;
                left: 28px; /* Standard offset */
                cursor: pointer;
                transition: background 0.1s;
            }
            .black-key:active, .black-key.active {
                background: #333;
            }
            .active {
                background: #bbf !important;
            }
            .black-key.active {
                background: #558 !important;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const noteNames = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
                const notes = [];

                for (let i = 0; i < 88; i++) {
                    const noteName = noteNames[(i + 9) % 12];
                    const octave = Math.floor((i + 9) / 12);
                    notes.push({
                        note: noteName,
                        octave: octave,
                        full: noteName + octave
                    });
                }

                // Render Piano
                const pianoContainer = document.getElementById('piano-container');
                pianoContainer.innerHTML = '';
                
                notes.forEach((n, i) => {
                    const isBlack = n.note.includes('#');
                    if (!isBlack) {
                        const key = document.createElement('div');
                        key.className = 'white-key';
                        key.dataset.note = n.full;
                        key.innerHTML = `<span class="mt-auto mb-2 text-xs text-gray-400 select-none">${n.full}</span>`;
                        key.addEventListener('mousedown', () => playNote(n.full));
                        
                        // Check if NEXT note is black (its sharp)
                        const nextIndex = i + 1;
                        if (nextIndex < notes.length) {
                            const nextN = notes[nextIndex];
                            if (nextN.note.includes('#')) {
                                const bKey = document.createElement('div');
                                bKey.className = 'black-key';
                                bKey.dataset.note = nextN.full;
                                bKey.addEventListener('mousedown', (e) => {
                                    e.stopPropagation(); 
                                    playNote(nextN.full);
                                });
                                key.appendChild(bKey);
                            }
                        }
                        pianoContainer.appendChild(key);
                    }
                });

                const logDiv = document.getElementById('log');
                const startBtn = document.getElementById('start-audio');
                const generateBtn = document.getElementById('generate-all');
                const audioStatus = document.getElementById('audio-status');

                function log(message) {
                    const div = document.createElement('div');
                    div.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
                    logDiv.prepend(div);
                }

                async function playNote(note) {
                    if (Tone.context.state !== 'running') {
                        await Tone.start();
                        audioStatus.textContent = "Audio Context Started (Auto)";
                        audioStatus.classList.remove('text-gray-500');
                        audioStatus.classList.add('text-green-500');
                    }
                    
                    const now = Tone.now();
                    const playbackSynth = new Tone.Synth().toDestination();
                    playbackSynth.triggerAttackRelease(note, "8n", now);
                    
                    log(`Playing ${note}`);
                    
                    // Highlight logic
                    const wKey = document.querySelector(`.white-key[data-note="${note}"]`);
                    const bKey = document.querySelector(`.black-key[data-note="${note}"]`);
                    
                    if(wKey) {
                         wKey.classList.add('active');
                         setTimeout(() => wKey.classList.remove('active'), 200);
                    }
                    if(bKey) {
                         bKey.classList.add('active');
                         setTimeout(() => bKey.classList.remove('active'), 200);
                    }
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
                        // Highlight
                        const wKey = document.querySelector(`.white-key[data-note="${n.full}"]`);
                        const bKey = document.querySelector(`.black-key[data-note="${n.full}"]`);
                        if(wKey) wKey.classList.add('active');
                        if(bKey) bKey.classList.add('active');

                        await generateAndUploadNote(n.note, n.octave);
                        
                        if(wKey) setTimeout(() => wKey.classList.remove('active'), 100);
                        if(bKey) setTimeout(() => bKey.classList.remove('active'), 100);

                        await new Promise(r => setTimeout(r, 100)); // Faster 100ms gap
                    }

                    log("All notes processed!");
                    generateBtn.disabled = false;
                });

                async function generateAndUploadNote(noteName, octave) {
                    const fullName = `${noteName}${octave}`;
                    log(`Generating ${fullName}...`);

                    const dest = Tone.context.createMediaStreamDestination();
                    const recorder = new MediaRecorder(dest.stream);
                    const synth = new Tone.Synth().connect(dest);

                    return new Promise((resolve) => {
                        const chunks = [];
                        recorder.ondataavailable = evt => chunks.push(evt.data);
                        recorder.onstop = async () => {
                            const blob = new Blob(chunks, {
                                type: 'audio/webm'
                            });
                            await uploadNote(noteName, octave, blob);
                            resolve();
                        };

                        recorder.start();
                        const now = Tone.now();
                        synth.triggerAttackRelease(fullName, "2n", now);

                        setTimeout(() => {
                            recorder.stop();
                        }, 1000); // 1s
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
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            // success
                        } else {
                            log(`Error uploading ${noteName}${octave}: ${JSON.stringify(data)}`);
                        }
                    } catch (error) {
                        log(`Network error uploading ${noteName}${octave}: ${error}`);
                    }
                }
            });
        </script>
    </div>
</x-filament-panels::page>