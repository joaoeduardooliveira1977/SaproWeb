<div>
    {{-- Card Análise IA --}}
    <div id="analise-ia" style="margin-top:16px;background:#f0f7ff;border:1.5px solid #bfdbfe;border-radius:12px;overflow:hidden;">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:linear-gradient(135deg,#1e40af 0%,#4f46e5 100%);">
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
                    </svg>
                </span>
                <div>
                    <div style="font-size:14px;font-weight:700;color:#fff;">Análise IA</div>
                    @if($analiseEm)
                    <div style="font-size:11px;color:rgba(255,255,255,.7);">Gerado em {{ $analiseEm }}</div>
                    @else
                    <div style="font-size:11px;color:rgba(255,255,255,.6);">Análise automática por Gemini</div>
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                @if(tenant_pode('ia'))
                    @if($analise)
                    <button wire:click="gerarAnalise" wire:loading.attr="disabled"
                        style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);border-radius:7px;color:#fff;font-size:12px;font-weight:600;cursor:pointer;transition:background .15s;"
                        onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">
                        <svg wire:loading.remove wire:target="gerarAnalise" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                        <svg wire:loading wire:target="gerarAnalise" style="animation:iapin .7s linear infinite;" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                        <span wire:loading.remove wire:target="gerarAnalise">Atualizar</span>
                        <span wire:loading wire:target="gerarAnalise">Aguarde...</span>
                    </button>
                    @else
                    <button wire:click="gerarAnalise" wire:loading.attr="disabled"
                        style="display:inline-flex;align-items:center;gap:6px;padding:7px 18px;background:#fff;border:none;border-radius:7px;color:#1e40af;font-size:13px;font-weight:700;cursor:pointer;transition:opacity .15s;"
                        wire:loading.class="opacity-50">
                        <svg wire:loading.remove wire:target="gerarAnalise" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                        </svg>
                        <svg wire:loading wire:target="gerarAnalise" style="animation:iapin .7s linear infinite;" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                        <span wire:loading.remove wire:target="gerarAnalise">Gerar Análise</span>
                        <span wire:loading wire:target="gerarAnalise">Analisando...</span>
                    </button>
                    @endif
                @else
                <div style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#f1f5f9;border:1.5px solid #e2e8f0;border-radius:8px;color:#94a3b8;font-size:12px;cursor:not-allowed;"
                    title="IA disponível nos planos Starter e Pro">
                    🔒 IA — Upgrade necessário
                </div>
                @endif
            </div>
        </div>

        {{-- Loading bar --}}
        <div wire:loading wire:target="gerarAnalise" style="height:3px;background:linear-gradient(90deg,#3b82f6,#8b5cf6,#3b82f6);background-size:200%;animation:iabar 1.4s linear infinite;"></div>

        {{-- Erro --}}
        @if($erro)
        <div style="display:flex;align-items:center;gap:10px;padding:12px 18px;background:#fef2f2;border-top:1px solid #fecaca;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span style="font-size:13px;color:#dc2626;">{{ $erro }}</span>
        </div>
        @endif

        {{-- Resultado --}}
        @if($analise)
        @php
            // Divide nas 4 seções
            $secoes = [];
            $titulos = ['RESUMO' => 'Resumo', 'RISCO' => 'Análise de Risco', 'PRÓXIMOS PASSOS' => 'Próximos Passos', 'ALERTAS' => 'Alertas'];
            $icones  = [
                'RESUMO'          => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>',
                'RISCO'           => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>',
                'PRÓXIMOS PASSOS' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>',
                'ALERTAS'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>',
            ];
            $cores = ['RESUMO' => '#1e40af', 'RISCO' => '#b45309', 'PRÓXIMOS PASSOS' => '#065f46', 'ALERTAS' => '#7c2d12'];
            $fundos = ['RESUMO' => '#eff6ff', 'RISCO' => '#fffbeb', 'PRÓXIMOS PASSOS' => '#f0fdf4', 'ALERTAS' => '#fff7ed'];

            $texto = $analise;
            foreach (array_keys($titulos) as $chave) {
                $pattern = '/' . preg_quote($chave, '/') . '\s*:\s*/iu';
                $texto = preg_replace($pattern, "|||{$chave}|||", $texto);
            }
            $partes = explode('|||', $texto);
            $chaveAtual = null;
            foreach ($partes as $parte) {
                if (isset($titulos[$parte])) {
                    $chaveAtual = $parte;
                } elseif ($chaveAtual) {
                    $secoes[$chaveAtual] = trim($parte);
                    $chaveAtual = null;
                }
            }
            if (empty($secoes)) {
                $secoes['RESUMO'] = $analise;
            }
        @endphp

        <div style="padding:18px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            @foreach($titulos as $chave => $label)
            @if(isset($secoes[$chave]))
            <div style="background:{{ $fundos[$chave] ?? '#f8fafc' }};border-radius:10px;padding:14px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ $cores[$chave] ?? '#64748b' }}" stroke-width="1.5">
                        {!! $icones[$chave] ?? '' !!}
                    </svg>
                    <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:{{ $cores[$chave] ?? '#64748b' }};">{{ $label }}</span>
                </div>
                <div style="font-size:13px;color:#334155;line-height:1.6;white-space:pre-line;">{{ $secoes[$chave] }}</div>
            </div>
            @endif
            @endforeach

            {{-- Fallback se não teve seções --}}
            @if(count($secoes) === 1 && isset($secoes['RESUMO']))
            @endif
        </div>

        @elseif(!$gerando && !$erro)
        <div style="padding:32px;text-align:center;color:#64748b;">
            <svg style="margin:0 auto 12px;display:block;opacity:.35;" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
            </svg>
            <div style="font-size:14px;font-weight:600;margin-bottom:4px;">Nenhuma análise gerada</div>
            <div style="font-size:12px;">Clique em "Gerar Análise" para obter uma análise inteligente deste processo.</div>
        </div>
        @endif

    </div>

    <style>
    @keyframes iapin { to { transform: rotate(360deg); } }
    @keyframes iabar { 0%{background-position:0% 50%} 100%{background-position:200% 50%} }
    @media(max-width:768px){ .ia-grid-2{grid-template-columns:1fr!important;} }
    </style>
</div>
