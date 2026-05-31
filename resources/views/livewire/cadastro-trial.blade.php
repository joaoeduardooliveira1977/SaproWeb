{{-- CadastroTrial — layouts.guest já fornece fundo e flex-center no body --}}
<div>
<style>
  .ct-card { background:#fff; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.3); padding:48px 40px; max-width:520px; width:100%; }
  .ct-logo { text-align:center; margin-bottom:32px; }
  .ct-logo-icon { font-size:48px; }
  .ct-logo h1 { font-size:24px; font-weight:700; color:#0f2540; margin-top:8px; }
  .ct-logo p  { color:#64748b; font-size:14px; margin-top:4px; }
  .ct-badge { display:inline-block; background:#d1fae5; color:#065f46; font-size:12px; font-weight:600; padding:4px 12px; border-radius:20px; margin-top:8px; }
  .ct-form .form-group { margin-bottom:20px; }
  .ct-form label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
  .ct-form input, .ct-form select { width:100%; padding:10px 14px; border:1.5px solid #d1d5db; border-radius:8px; font-size:15px; outline:none; transition:border-color .2s; background:#fff; }
  .ct-form input:focus, .ct-form select:focus { border-color:#1a3a5c; }
  .ct-form .row-2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
  .ct-form .terms { display:flex; align-items:flex-start; gap:10px; font-size:13px; color:#6b7280; }
  .ct-form .terms input { width:18px; height:18px; flex-shrink:0; margin-top:2px; }
  .ct-form .terms a { color:#1a3a5c; text-decoration:underline; }
  .ct-btn { width:100%; padding:14px; background:#1a3a5c; color:#fff; border:none; border-radius:8px; font-size:16px; font-weight:700; cursor:pointer; transition:background .2s; margin-top:8px; }
  .ct-btn:hover { background:#0f2540; }
  .ct-btn:disabled { opacity:.6; cursor:not-allowed; }
  .ct-login { text-align:center; margin-top:20px; font-size:14px; color:#6b7280; }
  .ct-login a { color:#1a3a5c; font-weight:600; text-decoration:none; }
  .error-msg { color:#dc2626; font-size:12px; margin-top:4px; }
  @media(max-width:480px) {
    .ct-card { padding:32px 20px; }
    .ct-form .row-2 { grid-template-columns:1fr; }
  }
</style>

<div class="ct-card">
  <div class="ct-logo">
    <div class="ct-logo-icon">⚖️</div>
    <h1>Software Jurídico</h1>
    <p>Sistema de Gestão para Escritórios de Advocacia</p>
    <span class="ct-badge">✨ 15 dias grátis — sem cartão</span>
  </div>

  @if(session('sucesso'))
    <div style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:8px;padding:16px;margin-bottom:20px;color:#065f46;font-size:14px;">
      {{ session('sucesso') }}
    </div>
  @endif

  <form wire:submit="cadastrar" class="ct-form" novalidate>

    <div class="form-group">
      <label>Nome completo *</label>
      <input type="text" wire:model="responsavel_nome" placeholder="Seu nome completo" autocomplete="name">
      @error('responsavel_nome') <span class="error-msg">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
      <label>Nome do escritório *</label>
      <input type="text" wire:model="nome_escritorio" placeholder="Silva & Advogados" autocomplete="organization">
      @error('nome_escritorio') <span class="error-msg">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
      <label>Email de acesso *</label>
      <input type="email" wire:model="email" placeholder="seu@email.com.br" autocomplete="email">
      @error('email') <span class="error-msg">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
      <label>WhatsApp / Telefone</label>
      <input type="tel" wire:model="telefone" placeholder="(11) 99999-9999" autocomplete="tel">
      @error('telefone') <span class="error-msg">{{ $message }}</span> @enderror
    </div>

    <div class="form-group row-2">
      <div>
        <label>Cidade</label>
        <input type="text" wire:model="cidade" placeholder="São Paulo">
      </div>
      <div>
        <label>Estado *</label>
        <select wire:model="estado">
          <option value="">Selecione</option>
          @foreach(['AC','AL','AM','AP','BA','CE','DF','ES','GO','MA','MG','MS','MT','PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SE','SP','TO'] as $uf)
            <option value="{{ $uf }}" @selected($estado === $uf)>{{ $uf }}</option>
          @endforeach
        </select>
        @error('estado') <span class="error-msg">{{ $message }}</span> @enderror
      </div>
    </div>

    <div class="form-group row-2">
      <div>
        <label>Senha *</label>
        <input type="password" wire:model="senha" placeholder="Mínimo 8 caracteres" autocomplete="new-password">
        @error('senha') <span class="error-msg">{{ $message }}</span> @enderror
      </div>
      <div>
        <label>Confirmar senha *</label>
        <input type="password" wire:model="senha_confirmacao" placeholder="Repita a senha" autocomplete="new-password">
        @error('senha_confirmacao') <span class="error-msg">{{ $message }}</span> @enderror
      </div>
    </div>

    <div class="form-group">
      <label class="terms">
        <input type="checkbox" wire:model="aceitar_termos">
        <span>Li e aceito os <a href="#" target="_blank">termos de uso</a> e a <a href="#" target="_blank">política de privacidade</a></span>
      </label>
      @error('aceitar_termos') <span class="error-msg">{{ $message }}</span> @enderror
    </div>

    <button type="submit" class="ct-btn" wire:loading.attr="disabled">
      <span wire:loading.remove>🚀 Começar gratuitamente — 15 dias grátis</span>
      <span wire:loading>⏳ Criando sua conta...</span>
    </button>
  </form>

  <div class="ct-login">
    Já tem conta? <a href="{{ route('login') }}">Fazer login</a>
  </div>
</div>
</div>
