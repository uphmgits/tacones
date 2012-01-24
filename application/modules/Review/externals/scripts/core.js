
en4.review = {

  urls : {
    vote : 'reviews/vote/',
    unvote: 'reviews/unvote',
    login : 'login'
  },

  data : {},

  vote: function(identity, helpful) {
    if( !en4.user.viewer.id ) {
      window.location.href = this.urls.login + '?return_url=' + encodeURIComponent(window.location.href);
      return;
    }

    var reviewFeedbackContainer = $('review_vote_feedback_review_' + identity);

    
    en4.core.request.send(new Request.HTML({
        'url' : this.urls.vote,
        'data' : {
          'format' : 'html',
          'review_id' : identity,
          'helpful' : helpful
        }
      }), {
        'element' : reviewFeedbackContainer
      });    

  },
  
  unvote: function(identity) {
    if( !en4.user.viewer.id ) {
      window.location.href = this.urls.login + '?return_url=' + encodeURIComponent(window.location.href);
      return;
    }

    var reviewFeedbackContainer = $('review_vote_feedback_review_' + identity);

    en4.core.request.send(new Request.HTML({
        'url' : this.urls.unvote,
        'data' : {
          'format' : 'html',
          'review_id' : identity
        }
      }), {
        'element' : reviewFeedbackContainer
      });    
  }
  
};
