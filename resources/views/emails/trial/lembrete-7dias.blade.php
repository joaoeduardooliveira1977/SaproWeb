@extends('emails.trial.layout')

@section('conteudo')
<h2>{{ $nomeResponsavel }}, como está sendo a experiência? 🤔</h2>

<p>Você está com o Software Jurídico há 7 dias. Queremos saber como está sendo!</p>

<p>Sua opinião é muito importante para continuarmos melhorando o sistema para escritórios de advocacia como o seu.</p>

<div class="highlight">
  <strong>Ainda tem {{ $diasRestantes }} dias de trial gratuito.</strong><br>
  Use esse tempo para explorar os módulos mais úteis para o seu dia a dia.
</div>

<p><strong>Módulos que os advogados mais usam:</strong></p>
<ul>
  <li>⏰ <strong>Prazos e Agenda</strong> — nunca perca um prazo</li>
  <li>💰 <strong>Honorários</strong> — controle financeiro completo</li>
  <li>📄 <strong>Documentos</strong> — todos os documentos em um lugar</li>
  <li>📊 <strong>Relatórios</strong> — para decisões inteligentes</li>
</ul>

<p style="text-align:center;margin:28px 0;">
  <a href="{{ $linkAcesso }}" class="btn" style="margin-right:12px;">Continuar explorando →</a>
  <a href="{{ $linkWhatsapp }}" class="btn btn-whatsapp">Falar com suporte</a>
</p>

<p>Se tiver alguma dificuldade ou sugestão, clique em "Falar com suporte" acima — respondemos rapidamente pelo WhatsApp!</p>
@endsection
