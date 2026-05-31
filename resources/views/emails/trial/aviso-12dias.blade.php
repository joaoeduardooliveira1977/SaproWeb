@extends('emails.trial.layout')

@section('conteudo')
<div class="alert danger">
  ⚠️ <strong>Atenção:</strong> Seu trial expira em {{ $diasRestantes }} dias!
</div>

<h2>{{ $nomeResponsavel }}, não perca o acesso ao seu escritório</h2>

<p>Seu período de teste gratuito do Software Jurídico está chegando ao fim. Em <strong>{{ $diasRestantes }} dias</strong> você perderá o acesso ao sistema e a todos os dados cadastrados.</p>

<p><strong>O que você vai perder se não assinar:</strong></p>
<ul>
  <li>❌ Acesso aos processos cadastrados</li>
  <li>❌ Controle de prazos e agenda</li>
  <li>❌ Histórico de andamentos</li>
  <li>❌ Relatórios e documentos</li>
</ul>

<p><strong>Com o plano pago você garante:</strong></p>
<ul>
  <li>✅ Dados seguros e disponíveis 24/7</li>
  <li>✅ Sem limite de processos</li>
  <li>✅ Suporte prioritário</li>
  <li>✅ Atualizações automáticas</li>
  <li>✅ Integração DataJud e IA</li>
</ul>

<p style="text-align:center;margin:28px 0;">
  <a href="{{ $linkWhatsapp }}" class="btn btn-whatsapp">Falar com suporte agora →</a>
</p>

<p>Nossa equipe está pronta para encontrar o melhor plano para o seu escritório. Entre em contato antes que o acesso expire!</p>
@endsection
