/* BELAJAR PINTAR SD — Pengembang: Zainudin Larau — helper JS global (toast, konfirmasi, pratinjau foto). */
// assets/js/app.js
(function(){
  function toast(msg, type){
    type = type || 'info';
    var t = document.createElement('div');
    t.className = 'alert alert-' + type + ' position-fixed';
    t.style.cssText = 'top:70px;right:15px;z-index:9999;min-width:240px;box-shadow:0 4px 14px rgba(0,0,0,.2)';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function(){ t.remove(); }, 3500);
  }
  window.bpToast = toast;
  // cegah submit ganda
  document.addEventListener('submit', function(e){
    var f = e.target;
    if (f.dataset.locked === '1') { e.preventDefault(); return; }
    f.dataset.locked = '1';
    var btns = f.querySelectorAll('[type=submit]');
    for (var i=0;i<btns.length;i++){ btns[i].disabled = true; }
  });
  // pratinjau foto generik: <input class="bp-photo" data-preview="#prevId">
  document.addEventListener('change', function(e){
    var inp = e.target.closest ? e.target.closest('.bp-photo') : null;
    if (!inp) return;
    var box = document.querySelector(inp.getAttribute('data-preview') || '');
    if (!box) return;
    box.innerHTML = '';
    var files = inp.files || [];
    for (var i=0;i<files.length;i++){
      (function(file){
        var img = document.createElement('img');
        img.className = 'photo-thumb mb-2';
        var rd = new FileReader();
        rd.onload = function(){ img.src = rd.result; };
        rd.readAsDataURL(file);
        box.appendChild(img);
      })(files[i]);
    }
  });
  // badge notifikasi navbar
  function pollNotif(){
    var el = document.getElementById('notifCount');
    if (!el) return;
    fetch(el.getAttribute('data-url'), {credentials:'same-origin'})
      .then(function(r){ return r.json(); })
      .then(function(j){ el.textContent = j.unread || ''; el.style.display = j.unread ? '' : 'none'; })
      .catch(function(){});
  }
  pollNotif(); setInterval(pollNotif, 60000);
})();
