@extends('emails.trial.layout')

@section('conteudo')
<h2>Seu trial do Software Jurídico encerrou, {{ $nomeResponsavel }}</h2>

<p>Obrigado por experimentar o Software Jurídico durante esses 15 dias! Esperamos que tenha sido uma experiência positiva para o <strong>{{ $nomeEscritorio }}</strong>.</p>

<p>Seus dados estão seguros conosco. Assim que você reativar sua conta, tudo estará exatamente como deixou — processos, clientes, documentos e prazos.</p>

<div class="highlight">
  <strong>Reativar é simples e rápido:</strong><br>
  1. Entre em contato pelo WhatsApp ou email<br>
  2. Escolha o plano ideal para seu escritório<br>
  3. Acesse imediatamente com todos os seus dados
</div>

<p style="text-align:center;margin:28px 0;">
  <a href="{{ $linkWhatsapp }}" class="btn btn-whatsapp" style="margin-bottom:12px;display:block;width:fit-content;margin-left:auto;margin-right:auto;">
    💬 Falar no WhatsApp
  </a>
  <a href="mailto:{{ $emailSuporte }}" class="btn" style="display:block;width:fit-content;margin-left:auto;margin-right:auto;">
    📧 Enviar email
  </a>
</p>

<p>Agradecemos pela confiança. Esperamos ter você de volta em breve!</p>
@endsection
