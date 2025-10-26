<<<<<<< HEAD

// app.js - improved: cart handling, totals, images fallback, categories and menu rendering
async function loadCategories(){
  const res = await fetch('api/categories.php');
  const cats = await res.json();
  const aside = document.getElementById('categories');
  aside.innerHTML = '<button onclick="loadMenu(0)">Todos</button>';
  cats.forEach(c=>{
    const b = document.createElement('button');
    b.textContent = c.name;
    b.onclick = ()=> loadMenu(c.id);
    aside.appendChild(b);
  });
}

async function loadMenu(categoryId=0){
  const url = categoryId ? `api/dishes.php?category=${categoryId}` : 'api/dishes.php';
  const res = await fetch(url);
  const dishes = await res.json();
  const menu = document.getElementById('menu');
  menu.innerHTML = '';
  dishes.forEach(d=>{
    const card = document.createElement('div');
    card.className = 'card';
    const imgUrl = d.image_url || 'https://source.unsplash.com/800x600/?food,restaurant';
    // ensure URL starts with http/https
    const finalImg = (/^https?:\/\//i.test(imgUrl)) ? imgUrl : 'https://' + imgUrl.replace(/^\/+/, '');
    card.innerHTML = `
      <img src="${finalImg}" alt="${(d.name||'Prato').replace(/"/g,'')}" onerror="this.onerror=null;this.src='https://source.unsplash.com/800x600/?food';">
      <h3>${d.name}</h3>
      <p>${d.description||''}</p>
      <div class="price">R$ ${parseFloat(d.price).toFixed(2)}</div>
      <button class="btn" onclick="addToCart(${d.id}, ${parseFloat(d.price)}, ${JSON.stringify(d.name)})">Adicionar</button>
    `;
    menu.appendChild(card);
  });
  updateCartCount();
}

function getCart(){ 
  try{
    return JSON.parse(localStorage.getItem('cart')||'[]');
  }catch(e){
    return [];
  }
}
function setCart(c){ localStorage.setItem('cart', JSON.stringify(c)); updateCartCount(); }

function updateCartCount(){
  const el=document.getElementById('cart-count'); 
  if(!el) return;
  const totalQty = getCart().reduce((s,i)=>s + (i.qty||0),0);
  el.textContent = totalQty;
}

function addToCart(id, price, name){
  const cart = getCart();
  const it = cart.find(x=>x.id===id);
  if(it){
    it.qty = (it.qty||0) + 1;
  } else {
    cart.push({id:id, qty:1, price: parseFloat(price), name:name});
  }
  setCart(cart);
  // if on cart page, refresh UI
  if(document.getElementById('cart-items')) renderCart();
  alert('Adicionado ao carrinho: ' + name);
}

function renderCart(){
  const container = document.getElementById('cart-items');
  if(!container) return;
  const cart = getCart();
  container.innerHTML = '';
  if(!cart.length){ 
    container.innerHTML='<p>Carrinho vazio</p>'; 
    document.getElementById('total').textContent='0.00'; 
    return; 
  }
  let total=0;
  cart.forEach(it=>{
    total += (it.qty * parseFloat(it.price||0));
    const div = document.createElement('div');
    div.className = 'cart-item';
    div.innerHTML = `<strong>${it.name}</strong> — Qtd: ${it.qty} — R$ ${(it.qty*it.price).toFixed(2)}
      <button onclick="changeQty(${it.id}, -1)">-</button>
      <button onclick="changeQty(${it.id}, 1)">+</button>
      <button onclick="removeItem(${it.id})">Remover</button>
    `;
    container.appendChild(div);
  });
  document.getElementById('total').textContent = total.toFixed(2);
}

function changeQty(id, delta){
  const cart = getCart();
  const it = cart.find(x=>x.id===id);
  if(!it) return;
  it.qty = Math.max(0, (it.qty||0) + delta);
  if(it.qty===0){
    const idx = cart.findIndex(x=>x.id===id);
    if(idx>=0) cart.splice(idx,1);
  }
  setCart(cart);
  renderCart();
}

function removeItem(id){
  const cart = getCart().filter(x=>x.id!==id);
  setCart(cart);
  renderCart();
}

async function createOrder(payload){
  const res = await fetch('api/create_order.php', {
    method:'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  });
  return res.json();
}

async function handleOrder(form){
  const data = new FormData(form);
  const payload = { name: data.get('name'), phone: data.get('phone'), cart: getCart() };
  const resp = await createOrder(payload);
  if(resp.error){ alert(resp.error); return; }
  localStorage.removeItem('cart');
  alert('Pedido criado. ID: ' + resp.orderId + ' — Total: R$ ' + parseFloat(resp.amount).toFixed(2));
  window.location.href = 'success.php';
}

document.addEventListener('DOMContentLoaded', ()=>{
  loadCategories();
  loadMenu();
  renderCart();
  const form = document.getElementById('order-form');
  if(form) form.addEventListener('submit', e=>{ e.preventDefault(); handleOrder(form); });
});
=======
// Função para alinhar os botões ao final do card
function alignButtons() {
    const cards = document.querySelectorAll('.dish');
    let maxHeight = 0;

    // Primeiro, resetamos para calcular corretamente
    cards.forEach(c => {
        const body = c.querySelector('.dish-body');
        body.style.height = 'auto';
    });

    // Encontrar a maior altura entre os cards visíveis
    cards.forEach(c => {
        if (c.style.display !== 'none') {
            const body = c.querySelector('.dish-body');
            if(body.offsetHeight > maxHeight) maxHeight = body.offsetHeight;
        }
    });

    // Aplicar altura máxima a todos os cards visíveis
    cards.forEach(c => {
        if (c.style.display !== 'none') {
            const body = c.querySelector('.dish-body');
            body.style.height = maxHeight + 'px';
        }
    });
}


// Filtrar por categoria
const buttons = document.querySelectorAll('#categories button');

buttons.forEach(btn => {
    btn.addEventListener('click', () => {
        const cat = btn.dataset.category;
        const cards = document.querySelectorAll('.dish');

        if(cat === 'all') {
            // Mostrar todos os cards
            cards.forEach(c => c.style.display = 'flex');
        } else {
            // Mostrar apenas a categoria selecionada
            cards.forEach(c => {
                c.style.display = (c.dataset.category === cat) ? 'flex' : 'none';
            });
        }

        // Recalcular alturas para alinhar botões
        alignButtons();
    });
});

// Alinha quando a página carrega
window.addEventListener('load', alignButtons);
window.addEventListener('resize', alignButtons); // também ajusta se a tela mudar
<<<<<<< HEAD
>>>>>>> ee5f96e (Foi alinhado os botões do cards da tela inicial, foi incluido a parte de checkout)
=======

>>>>>>> dea07ee (foi feito o alinhamento dos fotos dos cardapios e a parte de checkout)
