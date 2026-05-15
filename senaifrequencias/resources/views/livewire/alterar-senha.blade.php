<div class="max-w-lg">
    @if($sucesso)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="mb-5 flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            Senha alterada com sucesso!
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form wire:submit="salvar" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha atual</label>
                <input wire:model="senha_atual" type="password" autocomplete="current-password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai @error('senha_atual') border-red-400 @enderror">
                @error('senha_atual') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nova senha</label>
                <input wire:model="nova_senha" type="password" autocomplete="new-password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai @error('nova_senha') border-red-400 @enderror">
                @error('nova_senha') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar nova senha</label>
                <input wire:model="nova_senha_confirmation" type="password" autocomplete="new-password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai">
            </div>

            <div class="pt-1">
                <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-wait"
                        class="w-full sm:w-auto px-6 py-2 bg-senai hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <span wire:loading.remove>Alterar senha</span>
                    <span wire:loading>Salvando...</span>
                </button>
            </div>
        </form>
    </div>
</div>
