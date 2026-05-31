@extends('emails.trial.layout')

@section('conteudo')
<h2>🎉 Bem-vindo, {{ $nomeResponsavel }}!</h2>

<p>Sua conta no <strong>Software Jurídico</strong> foi criada com sucesso!
Você tem <strong>{{ $diasTrial }} dias gratuitos</strong> para explorar tudo o que o sistema oferece ao seu escritório.</p>

<div class="highlight">
  <strong>Seus dados de acesso:</strong><br>
  🔗 <a href="{{ $linkAcesso }}">{{ $linkAcesso }}</a><br>
  📧 Email: <strong>{{ $emailAcesso }}</strong><br>
  🔑 Senha: a senha que você cadastrou no formulário
</div>

<p style="text-align:center;margin:28px 0;">
  <a href="{{ $linkAcesso }}" class="btn">Acessar o sistema agora →</a>
</p>

<hr class="divider">

<p><strong>3 dicas para começar bem:</strong></p>
<ul>
  <li>📋 <strong>Cadastre um processo</strong> — vá em Processos → Novo Processo e preencha os dados do seu primeiro caso</li>
  <li>👤 <strong>Adicione seus clientes</strong> — em Pessoas você cadastra clientes, partes contrárias e advogados</li>
  <li>⏰ <strong>Configure seus prazos</strong> — nunca perca um prazo com o módulo de Prazos e Agenda</li>
</ul>

<p>Se precisar de ajuda, nossa equipe está disponível pelo WhatsApp ou email. Responda este email a qualquer momento.</p>

<p>Sucesso na advocacia! 🤝</p>
@endsection
