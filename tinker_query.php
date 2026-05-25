$contratos = DB::select("SELECT c.id, c.descricao, c.status, p.nome AS cliente_nome FROM contratos c JOIN pessoas p ON p.id = c.cliente_id WHERE c.status = 'encerrado' AND LOWER(p.nome) LIKE '%moema%'");
foreach ($contratos as $ct) { echo 'ID: '.(string)$ct->id.' | Status: '.(string)$ct->status.' | Cliente: '.(string)$ct->cliente_nome.' | Desc: '.(string)$ct->descricao.PHP_EOL; }
if (empty($contratos)) { echo 'Nenhum contrato encontrado.'.PHP_EOL; }
