<style>
    #blog_content {
        margin-top: 30px;
    }
    #blog_content .date {
        float: right;
        padding-left: 50px;
    }
    #blog_content div.post div {
        color: #d2d2d2;
    }
    #blog_content div.post {
        margin-bottom: 20px;
    }
    #blog_content .retweet {
        color: #09c;
    }
</style>

<div id="blog_content"></div>

<script type="text/javascript" src="http://demo.tumblr.com/api/read/json"></script>
<script type="text/javascript">
    console.log(tumblr_api_read);
    tumblr_api_read.posts.each( function (p) {
        post = new Element('div', {class: 'post'});
        date = new Element('div', {class: 'date'});
        date.innerHTML = p.date;
        date.inject(post);

        switch (p.type) {
            case 'quote':
                quote = new Element('div', {class: 'quote'});
                quote_text = new Element('div', {class: 'quote_text'});
                quote_source = new Element('div', {class: 'quote_source'});
                
                quote_text.innerHTML = p['quote-text'];
                quote_source.innerHTML = p['quote-source'];
                
                quote_text.inject(quote);
                quote_source.inject(quote);
                quote.inject(post);
                break;
            case 'photo':
                photo = new Element('div', {class: 'photo'});
                img = new Element('img', {src: p['photo-url-400']});
                a = new Element('a', {href: p['photo-url-1280']});
                caption = new Element('div', {class: 'caption'});
                
                caption.innerHTML = p['photo-caption'];
                img.inject(a);
                
                a.inject(photo);
                caption.inject(photo);
                photo.inject(post);
                break;
            case 'link':
                link = new Element('div', {class: 'link'});
                a = new Element('a', {href: p['link-url']});
                description = new Element('span', {class: 'description'});
                
                a.innerHTML = p['link-text'];
                description.innerHTML = p['link-description'];
                
                a.inject(link);
                description.inject(link);
                link.inject(post);
                break;
            case 'conversation':
                conversation = new Element('div', {class: 'conversation'});
                list = new Element('ul');
                p['conversation'].each( function (c) {
                    list_item = new Element('li');
                    list_item.innerHTML = c.label + " " + c.phrase;
                    list_item.inject(list);
                });
                list.inject(conversation);
                conversation.inject(post);
                break;
            case 'regular':
                regular = new Element('div', {class: 'regular'});
                header = new Element('h2');
                body = new Element('div', {class: 'description'});
                
                header.innerHTML = p['regular-title'];
                body.innerHTML = p['regular-body'];
                
                header.inject(regular);
                body.inject(regular);
                regular.inject(post);
                break;
            case 'audio':
                audio = new Element('div', {class: 'audio'});
                player = new Element('div', {class: 'player'});
                caption = new Element('div', {class: 'caption'});
                
                player.innerHTML = p['audio-player'];
                caption.innerHTML = p['audio-caption'];
                
                player.inject(audio);
                caption.inject(audio);
                audio.inject(post);
                break;
        }
        retweet_url = 'https://twitter.com/intent/tweet?original_referer=' + p['url'] + '&text=' + p['url'];
        retweet = new Element('a', {href: retweet_url, class: 'retweet'});
        retweet.innerHTML = 'retweet';
        retweet.inject(post);
        
        post.inject($('blog_content'));
    });
</script>