@extends('emails.trial.layout')

@section('conteudo')
<h2>Olá, {{ $nomeResponsavel }}! Aqui vai uma dica 💡</h2>

<p>Você já tem 3 dias de Software Jurídico. Ainda tem <strong>{{ $diasRestantes }} dias restantes</strong> no seu trial gratuito.</p>

<p>Hoje quero te mostrar como cadastrar seu primeiro processo em menos de 2 minutos:</p>

<div class="highlight">
  <strong>Passo a passo — Cadastrar um processo:</strong><br><br>
  1️⃣ No menu lateral, clique em <strong>Processos</strong><br>
  2️⃣ Clique no botão <strong>"+ Novo Processo"</strong><br>
  3️⃣ Preencha o número do processo, cliente e tipo<br>
  4️⃣ Defina a fase e grau de risco<br>
  5️⃣ Clique em <strong>Salvar</strong> — pronto!
</div>

<p>Depois de cadastrar o processo, você pode:</p>
<ul>
  <li>Adicionar andamentos e documentos</li>
  <li>Criar prazos vinculados ao processo</li>
  <li>Acompanhar movimentações automáticas via DataJud</li>
  <li>Gerar relatórios em PDF</li>
</ul>

<p style="text-align:center;margin:28px 0;">
  <a href="{{ $linkAcesso }}/processos/novo" class="btn">Criar meu primeiro processo →</a>
</p>

<p>Qualquer dúvida, é só responder este email. Estou aqui para ajudar! 😊</p>
@endsection
