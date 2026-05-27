<x-layout>

@php
    $isSuami = Auth::user()->role === 'suami';
@endphp

<div class="p-6">

    <!-- HEADER -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-3xl font-black text-deepBlue">
                Obrolan AI
            </h1>
            <p class="text-sm text-mutedGray mt-1">
                Chat dengan asisten kesehatan SatuHati
            </p>
        </div>
    </div>

    <!-- CHAT BOX -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6">

        <div id="chat-box" class="h-[400px] overflow-y-auto mb-4 space-y-3">

            @foreach($messages as $msg)

                <div class="{{ $msg->sender == 'user' ? 'text-right' : 'text-left' }}">

                    <div class="inline-block px-4 py-2 rounded-2xl
                        {{ $msg->sender == 'user'
                            ? ($isSuami ? 'bg-blue-500 text-white' : 'bg-softPink text-white')
                            : 'bg-pink-50 text-deepBlue border border-pink-100'
                        }}">

                        {{ $msg->message }}
                    </div>

                </div>

            @endforeach

        </div>

        <!-- INPUT -->
        <div class="flex gap-2">
            <input type="text" id="message"
                class="flex-1 border border-pink-100 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-pink-200"
                placeholder="Ketik pesan..."
                onkeydown="if(event.key==='Enter'){ sendMessage() }">

            <button onclick="sendMessage()"
                class="{{ $isSuami ? 'bg-blue-500 hover:bg-blue-600' : 'bg-softPink hover:bg-pink-500' }} text-white px-4 rounded-xl">
                Kirim
            </button>
        </div>

    </div>
</div>

<!-- ROLE JS -->
<script>
    const isSuami = @json($isSuami);
</script>

<!-- CHAT SCRIPT -->
<script>
async function sendMessage() {

    let input = document.getElementById('message');
    let chatBox = document.getElementById('chat-box');
    let message = input.value;

    if (!message || !message.trim()) return;

    try {
        let csrf = document.querySelector('meta[name="csrf-token"]')?.content;

        let response = await fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ message })
        });

        let data = await response.json();

        // USER MESSAGE
        chatBox.innerHTML += `
            <div class="text-right">
                <div class="inline-block ${isSuami ? 'bg-blue-500' : 'bg-softPink'} text-white px-4 py-2 rounded-2xl mb-2">
                    ${message}
                </div>
            </div>
        `;

        // BOT MESSAGE (tetap netral pink light)
        chatBox.innerHTML += `
            <div class="text-left">
                <div class="inline-block bg-pink-50 text-deepBlue px-4 py-2 rounded-2xl mb-2">
                    ${data.message}
                </div>
            </div>
        `;

        input.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;

    } catch (error) {
        console.error(error);
        alert("Gagal mengirim pesan");
    }
}
</script>

</x-layout>