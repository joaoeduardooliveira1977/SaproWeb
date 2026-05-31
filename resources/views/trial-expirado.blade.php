<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trial Expirado — Software Jurídico</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { background:linear-gradient(135deg,#0f2540,#1a3a5c); min-height:100vh; font-family:'Segoe UI',Arial,sans-serif; display:flex; align-items:center; justify-content:center; padding:24px; }
  .card { background:#fff; border-radius:20px; padding:56px 48px; max-width:560px; width:100%; text-align:center; box-shadow:0 24px 64px rgba(0,0,0,.3); }
  .icon { font-size:72px; margin-bottom:16px; display:block; }
  h1 { font-size:26px; font-weight:800; color:#0f2540; margin-bottom:12px; }
  p  { color:#64748b; font-size:15px; line-height:1.7; margin-bottom:24px; }
  .stats { display:flex; gap:16px; margin:24px 0; justify-content:center; }
  .stat { background:#f1f5f9; border-radius:12px; padding:16px 24px; flex:1; }
  .stat .num { font-size:28px; font-weight:800; color:#0f2540; }
  .stat .label { font-size:12px; color:#64748b; margin-top:4px; }
  .beneficios { text-align:left; background:#f8fafc; border-radius:12px; padding:20px 24px; margin:24px 0; }
  .beneficios h3 { font-size:14px; font-weight:700; color:#374151; margin-bottom:12px; }
  .beneficios ul { list-style:none; padding:0; }
  .beneficios ul li { font-size:14px; color:#374151; padding:5px 0; }
  .btns { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:8px; }
  .btn { display:inline-block; padding:14px 28px; border-radius:10px; font-size:15px; font-weight:700; text-decoration:none; cursor:pointer; border:none; }
  .btn-wa   { background:#25d366; color:#fff; }
  .btn-mail { background:#1a3a5c; color:#fff; }
  .btn-out  { background:#f1f5f9; color:#1a3a5c; font-size:13px; padding:10px 20px; }
  @media(max-width:480px) {
    .card { padding:36px 20px; }
    .stats { flex-direction:column; }
    .btns { flex-direction:column; }
    .btn { width:100%; text-align:center; }
  }
</style>
</head>
<body>
@php
  $tenant  = tenant();
  $usuario = auth('usuarios')->user();
  $processos = 0;
  $diasUso   = 0;
  if ($tenant) {
      try {
          $processos = \Illuminate\Support\Facades\DB::table('processos')
              ->where('tenant_id', $tenant->id)
              ->count();
          $diasUso = $tenant->trial_iniciado_em
              ? (int) $tenant->trial_iniciado_em->diffInDays(now())
              : 15;
      } catch (\Throwable) {}
  }
  $numero = preg_replace('/\D/', '', env('WHATSAPP_SUPORTE', '5511999999999'));
  $nomeEsc = $tenant?->nome ?? 'seu escritório';
  $msgWa = rawurlencode("Olá! Meu trial do Software Jurídico expirou ({$nomeEsc}) e quero reativar minha conta.");
  $linkWa = "https://wa.me/{$numero}?text={$msgWa}";
  $emailSuporte = env('EMAIL_SUPORTE', 'suporte@softwarejuridico.com.br');
@endphp
<div class="card">
  <span class="icon">⏰</span>
  <h1>Seu período de teste encerrou</h1>
  <p>O trial gratuito de 15 dias do <strong>{{ $nomeEsc }}</strong> chegou ao fim.<br>Seus dados estão seguros e disponíveis assim que você reativar a conta.</p>

  <div class="stats">
    <div class="stat">
      <div class="num">{{ $processos }}</div>
      <div class="label">processos cadastrados</div>
    </div>
    <div class="stat">
      <div class="num">{{ $diasUso }}</div>
      <div class="label">dias de uso</div>
    </div>
  </div>

  <div class="beneficios">
    <h3>✅ No plano pago você garante:</h3>
    <ul>
      <li>📋 Processos ilimitados</li>
      <li>👥 Múltiplos usuários</li>
      <li>🤖 Assistente jurídico com IA</li>
      <li>📊 DataJud — acompanhamento automático</li>
      <li>💬 Suporte prioritário</li>
      <li>🔒 Backup e segurança de dados</li>
    </ul>
  </div>

  <div class="btns">
    <a href="{{ $linkWa }}" target="_blank" rel="noopener" class="btn btn-wa">💬 Falar no WhatsApp</a>
    <a href="mailto:{{ $emailSuporte }}?subject=Reativar%20conta%20Software%20Jur%C3%ADdico&body={{ rawurlencode("Olá! Meu trial expirou ({$nomeEsc}) e quero reativar.") }}"
       class="btn btn-mail">📧 Enviar email</a>
  </div>

  <br>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-out">Sair do sistema</button>
  </form>
</div>
</body>
</html>
