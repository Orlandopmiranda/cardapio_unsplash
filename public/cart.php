<?php session_start(); ?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Carrinho</title><link rel="stylesheet" href="assets/css/styles.css"></head>
<body>
<h2>Carrinho</h2>
<div id="cart-items"></div>
<h3>Total: R$ <span id="total">0.00</span></h3>
<form id="order-form">
  <input name="name" placeholder="Seu nome" required><br>
  <input name="phone" placeholder="Telefone" required><br>
  <button type="submit">Criar pedido</button>
</form>
<script>document.addEventListener("DOMContentLoaded", ()=>{ renderCart(); });</script>
<a href="index.php">Voltar ao cardápio</a>
<script src="assets/js/app.js"></script>
</body></html>
