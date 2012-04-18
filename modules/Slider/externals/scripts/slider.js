var Slider=new Class({
    Implements:[Options],
    options:{
        slides:[],
        slider_url:"",
        current:1,
        delay:10000,
        first_description:null,
        url:""
    },
    block:false,
    pause_status:false,
    interval:null,
    description:[],
    initialize:function(a){
        this.setOptions(a);
        if(this.options.first_description!=null){
            this.description[this.options.current]=this.options.first_description
            }
            this.start()
        },
    change_img:function(f,e){
        if(!this.block && f!=this.options.current){
            this.block=true;
            if(typeof e=="undefined"){
                e=true
                }
                
                this.stop();
                var a=$("slide_"+this.options.slides[f-1]);
                if(a==null){
                    var d=$("slider_loading");
                    if(e){
                        d.setStyle("display","inline")
                        }
                        var b=this;
                    var c=new Request.JSON({
                        method:"post",
                        url:b.options.url,
                        data:{
                            slide_id:b.options.slides[f-1]
                            },
                        onComplete:function(h){
                            if($type(h)!="object"||!h.result||h.result!="success"){
                                return false
                                }else{
                                b.description[f]=h;
                                var g=new Element("div",{
                                    id:"slide_"+b.options.slides[f-1]
                                    });
                                if(h.link!=""){
                                    var i=new Element("a",{
                                        href:h.link
                                        })
                                    }
                                    new Asset.image(b.options.slider_url+b.options.slides[f-1]+".jpg",{
                                    "class":"slider",
                                    onload:function(j){
                                        if(h.link!=""){
                                            j.inject(i);
                                            i.inject(g)
                                            }else{
                                            j.inject(g)
                                            }
                                            if(e){
                                            d.setStyle("display","none")
                                            }
                                            b.show_next(g,f)
                                        }
                                    })
                            }
                        }
                    }).send()
        }else{
        this.show_next(a,f)
        }
    }

},
show_next:function(a,f){
    var d=a.getElement("img");
    d.setStyle("opacity",0);
    var c=$("slide_"+this.options.slides[this.options.current-1]);
    a.inject(c,"after");
    d.set("morph",{
        duration:600
    }).morph({
        opacity:1
    });
    $("slider_link_"+this.options.current).set("class","slider_off");
    $("slider_link_"+f).set("class","slider_on");
    this.options.current=f;
    this.block=false;

    var b=$("slider_content");
    if(this.description[f].title!=""||this.description[f].description!=""){
        b.setStyle('display', 'block');        
        $("slider_title").set("text",this.description[f].title);
        $("slider_description").set("html",this.description[f].description);

      }else{
        b.setStyle('display', 'none');       
        }
    if (this.pause_status == false) this.start();
    },
cycleForward:function(b){
    var a=this.options.current+1;
    if(a>this.options.slides.length){
        a=1
        }
        this.change_img(a,b)
    },

cycleBack:function(){
    var a=this.options.current-1;
    if(a<=0){
        a=this.options.slides.length
        }
        this.change_img(a)
    },
start:function(){
    var a=this;
    this.interval=setInterval(function(){
        a.cycleForward(false)
        },this.options.delay);
        $("slider_play_control_play").setStyle("display","none");$("slider_play_control_pause").setStyle("display","inline");
    },
stop:function(){
    clearInterval(this.interval)
    },
pause:function(){
    $("slider_play_control_pause").setStyle("display","none");
    $("slider_play_control_play").setStyle("display","inline");
    this.stop();
    this.pause_status = true;
    },
play:function(){
    $("slider_play_control_play").setStyle("display","none");
    $("slider_play_control_pause").setStyle("display","inline");
    this.pause_status = false;
    this.cycleForward();
    }
});