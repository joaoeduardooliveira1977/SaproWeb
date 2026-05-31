{{-- OnboardingChecklist — card de primeiros passos no dashboard --}}
@if($exibir && $visivel)
<div id="onboarding-checklist" style="background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:24px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,.06);">

  {{-- Header --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div>
      <h3 style="font-size:16px;font-weight:700;color:#0f2540;margin:0;">🎯 Primeiros passos</h3>
      <p style="font-size:13px;color:#64748b;margin:4px 0 0;">{{ $concluidos }}/{{ $total }} concluídos
        @if($diasRestantes > 0) — <span style="color:{{ $diasRestantes <= 3 ? '#dc2626' : ($diasRestantes <= 7 ? '#d97706' : '#059669') }};font-weight:600;">{{ $diasRestantes }} dias restantes de trial</span>@endif
      </p>
    </div>
    <button wire:click="ocultar" title="Ocultar" style="background:none;border:none;color:#94a3b8;cursor:pointer;font-size:18px;padding:4px;">✕</button>
  </div>

  {{-- Barra de progresso --}}
  <div style="background:#e2e8f0;border-radius:8px;height:8px;margin-bottom:20px;overflow:hidden;">
    <div style="width:{{ $percentual }}%;background:linear-gradient(90deg,#1a3a5c,#10b981);height:100%;border-radius:8px;transition:width .5s;"></div>
  </div>

  {{-- Lista de steps --}}
  <ul style="list-style:none;padding:0;margin:0;display:grid;gap:10px;">
    @foreach($steps as $key => $step)
    <li style="display:flex;align-items:center;gap:12px;padding:10px 14px;background:{{ $step['concluido'] ? '#f0fdf4' : '#f8fafc' }};border:1px solid {{ $step['concluido'] ? '#bbf7d0' : '#e2e8f0' }};border-radius:8px;">
      <span style="font-size:20px;flex-shrink:0;">{{ $step['concluido'] ? '✅' : '☐' }}</span>
      <span style="font-size:14px;color:{{ $step['concluido'] ? '#166534' : '#374151' }};{{ $step['concluido'] ? 'text-decoration:line-through;' : '' }}">
        {{ $step['descricao'] }}
      </span>
    </li>
    @endforeach
  </ul>

  @if($concluidos < $total)
  <div style="margin-top:16px;text-align:center;">
    <a href="{{ route('processos.novo') }}" style="display:inline-block;background:#1a3a5c;color:#fff;text-decoration:none;padding:10px 24px;border-radius:8px;font-size:13px;font-weight:600;margin-right:8px;">Criar primeiro processo</a>
    <a href="{{ route('pessoas') }}" style="display:inline-block;background:#f1f5f9;color:#1a3a5c;text-decoration:none;padding:10px 24px;border-radius:8px;font-size:13px;font-weight:600;">Cadastrar cliente</a>
  </div>
  @endif
</div>

{{-- Modal de conclusão --}}
@if($modalFim)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px;">
  <div style="background:#fff;border-radius:16px;padding:48px 40px;max-width:440px;width:100%;text-align:center;">
    <div style="font-size:64px;margin-bottom:16px;">🎉</div>
    <h2 style="font-size:22px;font-weight:700;color:#0f2540;margin-bottom:12px;">Parabéns! Você concluiu todos os passos!</h2>
    <p style="color:#64748b;font-size:15px;line-height:1.6;margin-bottom:24px;">
      Você conheceu os módulos principais do Software Jurídico.
      @if($diasRestantes > 0)
        Seu trial termina em <strong>{{ $diasRestantes }} dias</strong>. Fale conosco para continuar!
      @else
        Fale conosco para continuar usando o sistema!
      @endif
    </p>
    @php
      $numWa = preg_replace('/\D/', '', env('WHATSAPP_SUPORTE', '5511999999999'));
      $msgWa = rawurlencode('Olá! Completei o tour do Software Jurídico e quero continuar usando. Podemos conversar sobre os planos?');
    @endphp
    <a href="https://wa.me/{{ $numWa }}?text={{ $msgWa }}" target="_blank"
       style="display:inline-block;background:#25d366;color:#fff;text-decoration:none;padding:14px 32px;border-radius:8px;font-weight:700;font-size:15px;margin-bottom:12px;">
      💬 Falar no WhatsApp
    </a>
    <br>
    <button wire:click="fecharModalFim" style="background:none;border:none;color:#64748b;cursor:pointer;font-size:14px;margin-top:8px;">Fechar</button>
  </div>
</div>
@endif
@endif
