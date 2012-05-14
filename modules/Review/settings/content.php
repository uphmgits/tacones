<?php 

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Review
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

return array(

  // ------- on user profile sidebar
  array(
    'title' => 'Profile Review Rating',
    'description' => 'Displays a member\'s rating on their profile.',
    'category' => 'Reviews',
    'type' => 'widget',
    'name' => 'review.profile-rating',    
  ),

  // ------- on user profile tab

  array(
    'title' => 'Profile Reviews',
    'description' => 'Displays a member\'s reviews on their profile.',
    'category' => 'Reviews',
    'type' => 'widget',
    'name' => 'review.profile-reviews',
    'defaultParams' => array(
      'title' => 'Reviews',
      'titleCount' => true,
      'max' => 5,
      'showdetails' => 0,
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
            'value' => 'Reviews',
          )
        ),
        array(
          'Text',
          'max',
          array(
            'label' => 'Max Reviews',
            'value' => 5,
          ),
        ),
        array(
          'Select',
          'showdetails',
          array(
            'label' => 'Show Details',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 0,
          )
        ),               
      ),
    ),     
  ),
  
  // ------- most recommended members
  
  array(
    'title' => 'Most Recommended Members',
    'description' => 'Displays a list of most recommended members.',
    'category' => 'Reviews',
    'type' => 'widget',
    'name' => 'review.list-most-recommended',
    'defaultParams' => array(
      'title' => 'Most Recommended',
      'max' => 5,
      'showphoto' => 1,
      'showstars' => 1,
      'showdetails' => 1,
    ),   
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
            'value' => 'Most Recommended'
          )
        ),
        array(
          'Text',
          'max',
          array(
            'label' => 'Max Entries',
            'value' => 5,
          )
        ),   
        array(
          'Select', 
          'showphoto',
          array(
            'label' => 'Show Photo',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),
        array(
          'Select', 
          'showstars',
          array(
            'label' => 'Show Stars',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),
        array(
          'Select', 
          'showdetails',
          array(
            'label' => 'Show Details',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),          
      ),
    ),  
  ),  
  
  // ------- most reviewed members
  
  array(
    'title' => 'Most Reviewed Members',
    'description' => 'Displays a list of most reviewed members.',
    'category' => 'Reviews',
    'type' => 'widget',
    'name' => 'review.list-most-reviewed',
    'defaultParams' => array(
      'title' => 'Most Reviewed',
      'max' => 5,
      'showphoto' => 1,
      'showstars' => 1,
      'showdetails' => 1,
    ),   
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
            'value' => 'Most Reviewed'
          )
        ),
        array(
          'Text',
          'max',
          array(
            'label' => 'Max Entries',
            'value' => 5,
          )
        ),  
        array(
          'Select', 
          'showphoto',
          array(
            'label' => 'Show Photo',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),
        array(
          'Select', 
          'showstars',
          array(
            'label' => 'Show Stars',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),
        array(
          'Select', 
          'showdetails',
          array(
            'label' => 'Show Details',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),          
      ),
    ),  
  ),    
  
  // ------- top rated members
  
  array(
    'title' => 'Top Rated Members',
    'description' => 'Displays a list of top weighted average rating of members.',
    'category' => 'Reviews',
    'type' => 'widget',
    'name' => 'review.list-top-rated',
    'defaultParams' => array(
      'title' => 'Top Rated Members',
      'max' => 5,
      'showphoto' => 1,
      'showstars' => 1,
      'showdetails' => 1,
    ),   
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
            'value' => 'Top Rated Members'
          )
        ),
        array(
          'Text',
          'max',
          array(
            'label' => 'Max Entries',
            'value' => 5,
          )
        ), 
        array(
          'Select', 
          'showphoto',
          array(
            'label' => 'Show Photo',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),
        array(
          'Select', 
          'showstars',
          array(
            'label' => 'Show Stars',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),
        array(
          'Select', 
          'showdetails',
          array(
            'label' => 'Show Details',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),     
      ),
    ),  
  ), 
  
  // ------- top reviewers
  
  array(
    'title' => 'Top Reviewers',
    'description' => 'Displays a list of members who posted the most number of reviews.',
    'category' => 'Reviews',
    'type' => 'widget',
    'name' => 'review.list-top-reviewer',
    'defaultParams' => array(
      'title' => 'Top Reviewers',
      'max' => 5,
      'showphoto' => 1,
      'showdetails' => 1,
    ),   
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
            'value' => 'Top Reviewers'
          )
        ),
        array(
          'Text',
          'max',
          array(
            'label' => 'Max Entries',
            'value' => 5,
          )
        ),   
        array(
          'Select', 
          'showphoto',
          array(
            'label' => 'Show Photo',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),
        array(
          'Select', 
          'showdetails',
          array(
            'label' => 'Show Details',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),        
      ),
    ),  
  ),   
  
  // ------- list reviews
  
  array(
    'title' => 'List Reviews',
    'description' => 'Displays a list of posted reviews with different filtering options (can be used to build variety of review listings such as Recent Reviews, Recommended Reviews by XYZ user with specified rating etc..)',
    'category' => 'Reviews',
    'type' => 'widget',
    'name' => 'review.list-reviews',
    'defaultParams' => array(
      'title' => 'Recent Reviews',
      'max' => 10,
      'order' => 'recent',
      'display_style' => 'wide',
      'showphoto' => 1,
      'showdetails' => 1,
    ),  
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
            'value' => 'Recent Reviews',
          )
        ),
        array(
          'Text',
          'max',
          array(
            'label' => 'Max Reviews',
            'value' => 10,
          ),
        ),
        array(
          'Text',
          'owner',
          array(
            'label' => 'Reviewer',
          ),
        ), 
        array(
          'Text',
          'user',
          array(
            'label' => 'User',
          ),
        ),
        array(
          'Select',
          'recommend',
          array(
            'label' => 'Recommend',
            'multiOptions' => array(
              0 => "All reviews",
              1 => 'Only Recommended',
            ),
            'value' => 0,
          )
        ), 
        array(
          'Select', 
          'rating',
          array(
            'label' => 'Rating',
            'multiOptions' => array(
              0 => "All stars",
              '5' => '5 stars',
              '4' => '4 stars',
              '3' => '3 stars',
              '2' => '2 stars',
              '1' => '1 star',
            ),
            'value' => 0,
          )
        ),
        array(
          'Select', 
          'order',
          array(
            'label' => 'Sort By',
            'multiOptions' => array(
              'recent' => 'Most Recent',
              'ratingdesc' => 'Rating Descending',
              'ratingasc' => 'Rating Ascending',
              'helpfulnessdesc' => 'Helpfulness',
              'mosthelpful' => 'Most Helpful',
              'mostcommented' => 'Most Commented',
              'mostliked' => 'Most Liked',
            ),
            'value' => 'recent',
          )
        ),
        array(
          'Radio',
          'display_style',
          array(
            'label' => 'Display Style',
            'multiOptions' => array(
              'wide' => "Wide (main middle column)",
              'narrow' => "Narrow (left / side side column)",
            ),
            'value' => 'wide',
          )
        ),
        array(
          'Select', 
          'showphoto',
          array(
            'label' => 'Show Photo',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),  
        array(
          'Select', 
          'showdetails',
          array(
            'label' => 'Show Details',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),                                   
      ),
    ),    
  ),  

  // ------- top menu nav
  array(
    'title' => 'Menu Reviews',
    'description' => 'Displays a menu navigation (Browse Review, My Reviews, Post New Review) on review home page.',
    'category' => 'Reviews',
    'type' => 'widget',
    'name' => 'review.list-menu',
  ), 
  
  // ------- create new link
  array(
    'title' => 'Post New Review',
    'description' => 'Displays a quick navigation link to post new review',
    'category' => 'Reviews',
    'type' => 'widget',
    'name' => 'review.create-new',
  ),   
  
  // ------- search form
  
  array(
    'title' => 'Search Reviews',
    'description' => 'Displays search form on review home page.',
    'category' => 'Reviews',
    'type' => 'widget',
    'name' => 'review.search-form',
  ), 
  
  // ------- statistics
  
  array(
    'title' => 'Review Statistics',
    'description' => 'Displays review statistics (distribution histogram breakdown).',
    'category' => 'Reviews',
    'type' => 'widget',
    'name' => 'review.statistics',
    'defaultParams' => array(
      'title' => 'Review Statistics',
    ),
  ),  
  
  // ------- popular tags
  
  array(
    'title' => 'Review Popular Tags',
    'description' => 'Displays review popular tags.',
    'category' => 'Reviews',
    'type' => 'widget',
    'name' => 'review.popular-tags',
    'defaultParams' => array(
      'title' => 'Popular Tags',
      'max' => 50,
      'order' => 'text',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
            'value' => 'Popular Tags',
          )
        ),
        array(
          'Text',
          'max',
          array(
            'label' => 'Max Reviews',
            'value' => 50,
          ),
        ),
        array(
          'Select',
          'order',
          array(
            'label' => 'Order By',
            'multiOptions' => array(
              'text' => 'Tag Name',
              'total' => 'Total Count'
            ),
            'value' => 'text',
          )
        ),                
      ),
    ),     
  ),

  // ------- featured review
  array(
    'title' => 'Featured Review',
    'description' => 'Displays a randomized featured review.',
    'category' => 'Reviews',
    'type' => 'widget',
    'name' => 'review.featured-review',    
    'defaultParams' => array(
      'title' => 'Featured Review',
    ),  
  ),
);

