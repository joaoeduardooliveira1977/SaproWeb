<x-mail::message>
# Olá, {{ $mensalidade->cliente->nome }}

Confirmamos o recebimento do seu pagamento. ✅

<x-mail::panel>
**{{ $mensalidade->contratoMensal->descricao }}**

Competência: {{ $mensalidade->competenciaFormatada }}
Data de recebimento: {{ $mensalidade->data_recebimento->format('d/m/Y') }}
Valor recebido: R$ {{ number_format($mensalidade->valor_recebido, 2, ',', '.') }}
</x-mail::panel>

Obrigado pela pontualidade!

Atenciosamente,
{{ config('mail.from.name') }}
</x-mail::message>
