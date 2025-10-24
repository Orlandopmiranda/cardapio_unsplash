Projeto: Cardápio Online com Fotos do Unsplash + Categorias (PHP + MySQL + AJAX)

Notas rápidas:
- As imagens são carregadas diretamente do Unsplash via URLs (source.unsplash.com) — sem baixar arquivos.
- Há categorias (Massas, Carnes, Saladas, Sobremesas, Bebidas).
- Estrutura pronta para rodar localmente em XAMPP / LAMP.

Instruções de instalação resumidas:
1. Copie a pasta `public` para o diretório público do servidor (ex: htdocs/cardapio_unsplash).
2. Importe `sql/schema.sql` e `sql/sample_data.sql` no MySQL.
3. Edite `src/config.php` com as credenciais do seu banco de dados.
4. Acesse: http://localhost/cardapio_unsplash/public
