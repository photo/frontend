//browser-update.org notification script, <browser-update.org>
//Copyright (c) 2007-2009, MIT Style License <browser-update.org/LICENSE.txt>
var $buo = function(op,test) {
var jsv=5;
var n = window.navigator,b;
this.op=op||{};
//options
this.op.l = op.l||n["language"]||n["userLanguage"]||document.documentElement.getAttribute("lang")||"en";
this.op.vsakt = {i:9,f:10,o:12,s:5.1,n:20};
this.op.vsdefault = {i:6,f:3.5,o:10.1,s:3.2,n:10};
this.op.vs =op.vs||this.op.vsdefault;
for (b in this.op.vsakt)
    if (this.op.vs[b]>=this.op.vsakt[b])
        this.op.vs[b]=this.op.vsakt[b]-0.05;

if (!op.reminder || op.reminder<0.1 )
    this.op.reminder=0;
else
    this.op.reminder=op.reminder||24;

this.op.onshow = op.onshow||function(o){};
this.op.url= op.url||"http://browser-update.org/update.html";
this.op.pageurl = op.pageurl || window.location.hostname || "unknown";
this.op.newwindow=op.newwindow||false;

this.op.test=test||op.test||false;
if (window.location.hash=="#test-bu")
    this.op.test=true;

/*
if (op.new7 || (this.op.l=="de" && !this.op.test && Math.round(Math.random()*3)==1)) { //test new script
     var e = document.createElement("script");
     e.setAttribute("type", "text/javascript");
     e.setAttribute("src", "http://browser-update.org/update7.js");
     document.body.appendChild(e);
     return;
}
*/

function getBrowser() {
    var n,v,t,ua = navigator.userAgent;
    var names={i:'Internet Explorer',f:'Firefox',o:'Opera',s:'Apple Safari',n:'Netscape Navigator', c:"Chrome", x:"Other"};
    if (/like firefox|chromeframe|seamonkey|opera mini|meego|netfront|moblin|maemo|arora|camino|flot|k-meleon|fennec|kazehakase|galeon|android|mobile|iphone|ipod|ipad|epiphany|rekonq|symbian|webos/i.test(ua)) n="x";
    else if (/trident.(\d+\.\d+);/.test(ua)) n="io";
	else if (/MSIE (\d+\.\d+);/.test(ua)) n="i";
    else if (/Chrome.(\d+\.\d+)/i.test(ua)) n="c";
    else if (/Firefox.(\d+\.\d+)/i.test(ua)) n="f";
    else if (/Version.(\d+.\d+).{0,10}Safari/i.test(ua))	n="s";
    else if (/Safari.(\d+)/i.test(ua)) n="so";
    else if (/Opera.*Version.(\d+\.?\d+)/i.test(ua)) n="o";
    else if (/Opera.(\d+\.?\d+)/i.test(ua)) n="o";
    else if (/Netscape.(\d+)/i.test(ua)) n="n";
    else return {n:"x",v:0,t:names[n]};
    if (n=="x") return {n:"x",v:0,t:names[n]};
    
    v=new Number(RegExp.$1);
    if (n=="so") {
        v=((v<100) && 1.0) || ((v<130) && 1.2) || ((v<320) && 1.3) || ((v<520) && 2.0) || ((v<524) && 3.0) || ((v<526) && 3.2) ||4.0;
        n="s";
    }
    if (n=="i" && v==7 && window.XDomainRequest) {
        v=8;
    }
    if (n=="io") {
		n="i";
        if (v>4) v=9;
		else if (v>3.1) v=8;
		else if (v>3) v=7;
    }	
    return {n:n,v:v,t:names[n]+" "+v}
}

this.op.browser=getBrowser();
if (!this.op.test && (!this.op.browser || !this.op.browser.n || this.op.browser.n=="x" || this.op.browser.n=="c" || document.cookie.indexOf("browserupdateorg=pause")>-1 || this.op.browser.v>this.op.vs[this.op.browser.n]))
    return;

if (!this.op.test) {
    var i = new Image();
	//DISABLED TEMPORARYLY
    //i.src="http://browser-update.org/viewcount.php?n="+this.op.browser.n+"&v="+this.op.browser.v + "&p="+ escape(this.op.pageurl) + "&jsv="+jsv;
}
if (this.op.reminder>0) {
    var d = new Date(new Date().getTime() +1000*3600*this.op.reminder);
    document.cookie = 'browserupdateorg=pause; expires='+d.toGMTString()+'; path=/';
}
var ll=this.op.l.substr(0,2);
var languages = "de,en";
if (languages.indexOf(ll)!==false)
    this.op.url="http://browser-update.org/"+ll+"/update.html#"+jsv;
var tar="";
if (this.op.newwindow)
    tar=' target="_blank"';

function busprintf() {
    var args=arguments;
    var data = args[ 0 ];
    for( var k=1; k<args.length; ++k ) {
        data = data.replace( /%s/, args[ k ] );
    }
    return data;
}

var t = 'Your browser (%s) is <b>out of date</b>. It has known <b>security flaws</b> and may <b>not display all features</b> of this and other websites. \
         <a%s>Learn how to update your browser</a>';
if (ll=="de")
    t = 'Sie verwenden einen <b>veralteten Browser</b> (%s) mit <b>Sicherheitsschwachstellen</b> und <b>k&ouml;nnen nicht alle Funktionen dieser Webseite nutzen</b>. \
        <a%s>Hier erfahren Sie, wie einfach Sie Ihren Browser aktualisieren k&ouml;nnen</a>.';
else if (ll=="it")
    t = 'Il tuo browser (%s) <b>non è aggiornato</b>. Ha delle <b>falle di sicurezza</b> e potrebbe <b>non visualizzare correttamente</b> le \
        pagine di questo e altri siti. \
        <a%s>Aggiorna il tuo browser</a>!';
else if (ll=="pl")
    t = 'Przeglądarka (%s), której używasz, jest przestarzała. Posiada ona udokumentowane <b>luki bezpieczeństwa, inne wady</b> oraz <b>ograniczoną funkcjonalność</b>. Tracisz możliwość skorzystania z pełni możliwości oferowanych przez niektóre strony internetowe. <a%s>Dowiedz się jak zaktualizować swoją przeglądarkę</a>.';
else if (ll=="es")
    t = 'Tu navegador (%s) está <b>desactualizado</b>. Tiene conocidas <b>fallas de seguridad</b> y podría <b>no mostrar todas las características</b> de este y otros sitios web. <a%s>Aprénde cómo puedes actualizar tu navegador</a>';
else if (ll=="nl")
    t = 'Uw browser (%s) is <b>oud</b>. Het heeft bekende <b>veiligheidsissues</b> en kan <b>niet alle mogelijkheden</b> weergeven van deze of andere websites. <a%s>Lees meer over hoe uw browser te upgraden</a>';
else if (ll=="pt")
    t = 'Seu navegador (%s) está <b>desatualizado</b>. Ele possui <b>falhas de segurança</b> e pode <b>apresentar problemas</b> para exibir este e outros websites. <a%s>Veja como atualizar o seu navegador</a>';
else if (ll=="sl")
    t = 'Vaš brskalnik (%s) je <b>zastarel</b>. Ima več <b>varnostnih pomankljivosti</b> in morda <b>ne bo pravilno prikazal</b> te ali drugih strani. \
        <a%s>Poglejte kako lahko posodobite svoj brskalnik</a>';
else if (ll=="ru")
    t = 'Ваш браузер (%s) <b>устарел</b>. Он имеет <b>уязвимости в безопасности</b> и может <b>не показывать все возможности</b> на этом и других сайтах. <a%s>Узнайте, как обновить Ваш браузер</a>';
else if (ll=="id")
    t = 'Browser Anda (% s) sudah <b>kedaluarsa</b>. Browser yang Anda pakai memiliki <b>kelemahan keamanan</b> dan mungkin <b>tidak dapat menampilkan semua fitur</b> dari situs Web ini dan lainnya. <a%s> Pelajari cara memperbarui browser Anda</a>';
else if (ll=="uk")
    t = 'Ваш браузер (%s) <b>застарів</b>. Він <b>уразливий</b> й може <b>не відображати всі можливості</b> на цьому й інших сайтах. <a%s>Дізнайтесь, як оновити Ваш браузер</a>';
else if (ll=="ko")
    t = '지금 사용하고 계신 브라우저(%s)는 <b>오래되었습니다.</b> 알려진 <b>보안 취약점</b>이 존재하며, 새로운 웹 사이트가 <b>깨져 보일 수도</b> 있습니다. <a%s>브라우저를 어떻게 업데이트하나요?</a>';
else if (ll=="rm")
    t = 'Tes navigatur (%s) è <b>antiquà</b>. El cuntegna <b>problems da segirezza</b> enconuschents e mussa eventualmain <b>betg tut las funcziuns</b> da questa ed autras websites. <a%s>Emprenda sco actualisar tes navigatur</a>.';
else if (ll=="ja")	
	t = 'お使いのブラウザ「%s」は、<b>時代遅れ</b>のバージョンです。既知の<b>脆弱性</b>が存在するばかりか、<b>機能不足</b>によって、サイトが正常に表示できない可能性があります。 \
         <a%s>ブラウザを更新する方法を確認する</a>';
else if (ll=="fr")
	t = 'Votre navigateur (%s) est <b>périmé</b>. Il contient des <b>failles de sécurité</b> et pourrait <b>ne pas afficher certaines fonctionalités</b> des sites internet récents. <a%s>Découvrez comment mettre votre navigateur à jour</a>';
else if (ll=="da")
        t = 'Din browser (%s) er <b>forældet</b>. Den har kendte <b>sikkerhedshuller</b> og kan måske <b>ikke vise alle funktioner</b> på dette og andre websteder. <a%s>Se hvordan du opdaterer din browser</a>';
else if (ll=="al")
        t = 'Shfletuesi juaj (%s) është <b>ca i vjetër</b>. Ai ka <b>të meta sigurie</b> të njohura dhe mundet të <b>mos i shfaqë të gjitha karakteristikat</b> e kësaj dhe shumë faqeve web të tjera. <a%s>Mësoni se si të përditësoni shfletuesin tuaj</a>';
		
if (op.text)
    t = op.text;

this.op.text=busprintf(t,this.op.browser.t,' href="'+this.op.url+'"'+tar);

var div = document.createElement("div");
this.op.div = div;
div.id="buorg";
div.className="buorg";
div.innerHTML= '<div>' + this.op.text + '<div id="buorgclose">X</div></div>';

var sheet = document.createElement("style");
//sheet.setAttribute("type", "text/css");
var style = ".buorg {position:absolute;z-index:111111;\
width:100%; top:0px; left:0px; \
border-bottom:1px solid #A29330; \
background:#FDF2AB no-repeat 10px center url(http://browser-update.org/img/dialog-warning.gif);\
text-align:left; cursor:pointer; \
font-family: Arial,Helvetica,sans-serif; color:#000; font-size: 12px;}\
.buorg div { padding:5px 36px 5px 40px; } \
.buorg a,.buorg a:visited  {color:#E25600; text-decoration: underline;}\
#buorgclose { position: absolute; right: .5em; top:.2em; height: 20px; width: 12px; font-weight: bold;font-size:14px; padding:0; }";
document.body.insertBefore(div,document.body.firstChild);
document.getElementsByTagName("head")[0].appendChild(sheet);
try {
    sheet.innerText=style;
    sheet.innerHTML=style;
}
catch(e) {
    try {
        sheet.styleSheet.cssText=style;
    }
    catch(e) {
        return;
    }
}
var me=this;
div.onclick=function(){
    if (me.op.newwindow)
        window.open(me.op.url,"_blank");
    else
        window.location.href=me.op.url;
    return false;
};
div.getElementsByTagName("a")[0].onclick = function(e) {
    var e = e || window.event;
    if (e.stopPropagation) e.stopPropagation();
    else e.cancelBubble = true;
    return true;
}

this.op.bodymt = document.body.style.marginTop;
document.body.style.marginTop = (div.clientHeight)+"px";
document.getElementById("buorgclose").onclick = function(e) {
    var e = e || window.event;
    if (e.stopPropagation) e.stopPropagation();
    else e.cancelBubble = true;
    me.op.div.style.display="none";
    document.body.style.marginTop = me.op.bodymt;
    return true;
}
op.onshow(this.op);

}
var $buoop = $buoop||{};
$bu=$buo($buoop);