document.addEventListener('DOMContentLoaded',()=>{
  // Hero slider — fade + subtle zoom on the visual column
  const s=[...document.querySelectorAll('.hero-slide')];
  const box=document.querySelector('.dots');
  if(s.length&&box){
    let c=0,t;
    s.forEach((_,i)=>{
      let b=document.createElement('button');
      b.className='dot'+(i===0?' active':'');
      b.setAttribute('aria-label','Slide '+(i+1));
      b.onclick=()=>show(i);
      box.appendChild(b);
    });
    function show(n){
      s[c].classList.remove('active');
      box.children[c].classList.remove('active');
      c=n;
      s[c].classList.add('active');
      box.children[c].classList.add('active');
      // restart zoom animation on the visual
      const vis=s[c].querySelector('.sl-visual');
      if(vis){vis.style.animation='none';void vis.offsetWidth;vis.style.animation=''}
      clearInterval(t);
      t=setInterval(()=>show((c+1)%s.length),6000);
    }
    t=setInterval(()=>show((c+1)%s.length),6000);
  }

  // Wishlist toggle
  document.querySelectorAll('.wishlist-btn').forEach(btn=>{
    btn.addEventListener('click',function(e){
      e.preventDefault();
      const filled=this.textContent.trim()==='♥';
      this.textContent=filled?'♡':'♥';
      this.style.background=filled?'':'#0787f5';
      this.style.color=filled?'':'#fff';
    });
  });
});