const mobileMenuToggle=document.getElementById("mobileMenuToggle");
const siteNavigation=document.getElementById("siteNavigation");

if(mobileMenuToggle&&siteNavigation){
  const closeMobileMenu=()=>{
    siteNavigation.classList.remove("open");
    mobileMenuToggle.classList.remove("open");
    mobileMenuToggle.setAttribute("aria-expanded","false");
    mobileMenuToggle.setAttribute("aria-label","Open navigation");
  };

  mobileMenuToggle.addEventListener("click",()=>{
    const opening=!siteNavigation.classList.contains("open");
    siteNavigation.classList.toggle("open",opening);
    mobileMenuToggle.classList.toggle("open",opening);
    mobileMenuToggle.setAttribute("aria-expanded",String(opening));
    mobileMenuToggle.setAttribute("aria-label",opening?"Close navigation":"Open navigation");
  });

  siteNavigation.querySelectorAll("a").forEach(link=>link.addEventListener("click",closeMobileMenu));
  document.addEventListener("click",event=>{
    if(!siteNavigation.contains(event.target)&&!mobileMenuToggle.contains(event.target))closeMobileMenu();
  });
  document.addEventListener("keydown",event=>{
    if(event.key==="Escape")closeMobileMenu();
  });
}
