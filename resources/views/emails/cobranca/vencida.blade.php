<x-mail::message>
# Olá, {{ $mensalidade->cliente->nome }}

Identificamos uma parcela **em atraso** em sua conta.

<x-mail::panel>
**{{ $mensalidade->contratoMensal->descricao }}**

Competência: {{ $mensalidade->competenciaFormatada }}
Vencimento: {{ $mensalidade->vencimento->format('d/m/Y') }}
Valor: R$ {{ number_format($mensalidade->valor, 2, ',', '.') }}
Dias em atraso: {{ now()->diffInDays($mensalidade->vencimento) }} dia(s)
</x-mail::panel>

Entre em contato para regularizar sua situação.

Atenciosamente,
{{ config('mail.from.name') }}
</x-mail::message>
