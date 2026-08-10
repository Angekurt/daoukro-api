<x-filament-panels::page>

    {{-- Résumé financier --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Revenu mensuel estimé</p>
            <p class="text-2xl font-black text-primary-600">{{ $this->getRevenuEstime() }}</p>
            <p class="text-xs text-gray-400 mt-1">Basé sur les abonnés actifs</p>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Abonnés payants actifs</p>
            <p class="text-2xl font-black text-success-600">{{ $this->getNbAbonnes() }}</p>
            <p class="text-xs text-gray-400 mt-1">Standard + Pro + Business</p>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Expirent dans 7 jours</p>
            <p class="text-2xl font-black {{ $this->getNbExpirantBientot() > 0 ? 'text-warning-600' : 'text-gray-400' }}">
                {{ $this->getNbExpirantBientot() }}
            </p>
            <p class="text-xs text-gray-400 mt-1">À relancer</p>
        </div>
    </div>

    {{-- Table des abonnés --}}
    {{ $this->table }}

</x-filament-panels::page>
