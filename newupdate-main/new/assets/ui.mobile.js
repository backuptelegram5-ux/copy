
document.addEventListener('DOMContentLoaded',function(){
  var t=document.getElementById('mNavToggle');
  var sb=document.getElementById('mSidebar');
  if(!t||!sb) return;
  t.addEventListener('click',function(){
    var open=document.body.classList.toggle('nav-open');
    t.setAttribute('aria-expanded',open?'true':'false');
    sb.setAttribute('aria-hidden',open?'false':'true');
  });
  document.addEventListener('click',function(e){
    if(!document.body.classList.contains('nav-open')) return;
    if(e.target.closest('#mSidebar')||e.target.closest('#mNavToggle')) return;
    document.body.classList.remove('nav-open');
    t.setAttribute('aria-expanded','false');
    sb.setAttribute('aria-hidden','true');
  });
});
