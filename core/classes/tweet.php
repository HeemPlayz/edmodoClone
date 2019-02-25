<?php

    class Tweet extends User{

        function __construct($pdo){
            $this->pdo = $pdo;
        }

        public function tweets($user_id,$num){
            $stmt=$this->pdo->prepare("SELECT * FROM tweet LEFT JOIN users ON tweetBy=user_id WHERE tweetBy = :user_id AND retweetID = '0' OR tweetBy = user_id AND retweetBy != :user_id ORDER BY postedOn DESC LIMIT :num");
            $stmt->bindParam(":user_id",$user_id, PDO::PARAM_INT);
            $stmt->bindParam(":num",$num, PDO::PARAM_INT);
            $stmt->execute();
            $tweets = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            foreach($tweets as $tweet){ 
                $likes = $this->likes($user_id, $tweet->tweetID);
                $retweet = $this->checkRetweet($tweet->tweetID, $user_id);
                $user = $this->userData($tweet->retweetBy);
                echo '<div class="all-tweet">
                <div class="t-show-wrap">	
                 <div class="t-show-inner">
                  '.(($retweet['retweetID'] === $tweet->retweetID OR $tweet->retweetID > 0) ? '
                    <div class="t-show-banner">
                        <div class="t-show-banner-inner">
                            <span><i class="fa fa-retweet" aria-hidden="true"></i></span><span>'.$user->screenName.' Retweeted</span>
                        </div>
                    </div>'
                     : '').'

                     '.((!empty($tweet->retweetMsg) && $tweet->tweetID === $retweet['tweetID'] or $tweet->retweetID > 0)? 
                     
                     '<div class="t-show-popup" data-tweet="'.$tweet->tweetID.'">
                     <div class="t-show-head">
                     <div class="t-show-img">
                         <img src="'.BASE_URL.$user->profileImage.'"/>
                     </div>
                     <div class="t-s-head-content">
                         <div class="t-h-c-name">
                             <span><a href="'.BASE_URL.$user->username.'">'.$user->screenName.'</a></span>
                             <span>@'.$user->screenName.'</span>
                             <span>'.$this->timeAgo($retweet['postedOn']).'</span>
                         </div>
                         <div class="t-h-c-dis">
                             '.$this->getTweetLinks($tweet->retweetMsg).'
                         </div>
                     </div>
                 </div>
                 <div class="t-s-b-inner">
                     <div class="t-s-b-inner-in">
                         <div class="retweet-t-s-b-inner">
                         '.((!empty($tweet->tweetImage)) ? '   
                         <div class="retweet-t-s-b-inner-left">
                                 <img src="'.BASE_URL.$tweet->tweetImage.'" class="imagePopup" data-tweet="'.$tweet->tweetID.'"/>	
                             </div> ':'').' 
                             <div>
                                 <div class="t-h-c-name">
                                     <span><a href="'.BASE_URL.$tweet->screenName.'">'.$tweet->screenName.'</a></span>
                                     <span>@'.$tweet->username.'</span>
                                     <span>'.$this->timeAgo($tweet->postedOn).'</span>
                                 </div>
                                 <div class="retweet-t-s-b-inner-right-text">		
                                 '.$this->getTweetLinks($tweet->status).'
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 </div>' : '
                    <div class="t-show-popup" data-tweet="'.$tweet->tweetID.'">
                        <div class="t-show-head">
                            <div class="t-show-img">
                                <img src="'.$tweet->profileImage.'"/>
                            </div>
                            <div class="t-s-head-content">
                                <div class="t-h-c-name">
                                    <span><a href="'.$tweet->username.'">'.$tweet->screenName.'</a></span>
                                    <span>@'.$tweet->username.'</span>
                                    <span>'.$this->timeAgo($tweet->postedOn).'</span>
                                </div>
                                <div class="t-h-c-dis">
                                '.$this->getTweetLinks($tweet->status).'
                                </div>
                            </div>
                        </div>'.
                        ((!empty($tweet->tweetImage)) ? 
                            '<!--tweet show head end-->
                      <div class="t-show-body">
                          <div class="t-s-b-inner">
                           <div class="t-s-b-inner-in">
                             <img src="'.$tweet->tweetImage.'" class="imagePopup" data-tweet="'.$tweet->tweetID.'"/>
                           </div>
                          </div>
                        </div>
                        <!--tweet show body end-->
                        ' : '').'
                    
                   </div>').'
                    <div class="t-show-footer">
                        <div class="t-s-f-right">
                            <ul> 
                                <li><button><a href="#"><i class="fa fa-share" aria-hidden="true"></i></a></button></li>	
                                <li>'.(($tweet->tweetID===$retweet['retweetID']) ? '<button class="retweeted" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><a href="#"><i class="fa fa-retweet" aria-hidden="true"></i><span class="retweetsCount">'.$tweet->retweetCount.'</span> </a></button>' : '<button class="retweet" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><a href="#"><i class="fa fa-retweet" aria-hidden="true"></i><span class="retweetsCount">'.(($tweet->retweetCount>0)? $tweet->retweetCount : '').'</span> </a></button>' ).'</li>
                                <li>'.(($likes['LikedOn']===$tweet->tweetID) ? '<button class="unlike-btn" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'">
                                <a href="#"><i style="color:rgb(0, 132, 255);" class="fa fa-thumbs-o-up" aria-hidden="true"></i><span style="color:rgb(0, 0, 0);" class="likesCounter">'.$tweet->likesCount.'</span></a></button>' 
                                : '<button class="like-btn" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><a href="#">
                                <i style="color:rgb(0, 132, 255);" class="fa fa-thumbs-o-up" aria-hidden="true">
                                
                                </i><span style="color:rgb(0, 0, 0);" class="likesCounter">'.(($tweet->likesCount>0) ? $tweet->likesCount:'').'</span></a></button>').'</li>
                                '.(($tweet->tweetBy === $user_id)? '
                                    <li>
                                    <a href="#" class="more"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a>
                                    <ul> 
                                      <li><label class="deleteTweet" data-tweet="'.$tweet->tweetID.'">Delete Tweet</label></li>
                                    </ul>
                                </li>':'').'
                            </ul>
                        </div>
                    </div>
                </div>
                </div>
                </div>';
            }
        }

        public function getUserTweets($user_id){
            $stmt = $this->pdo->prepare("SELECT * FROM tweet LEFT JOIN users ON tweetBy = user_id WHERE tweetBy = ? AND retweetID = '0' OR retweetBy = ? ORDER BY postedOn DESC");
            $stmt->execute(array($user_id,$user_id));
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }

        public function addLike($user_id,$tweet_id,$get_id){
            $stmt=$this->pdo->prepare("UPDATE tweet SET likesCount=likesCount+1 WHERE tweetID=?");
            $stmt->execute([$tweet_id]);

            $this->create('likes',array('LikeBy'=>$user_id,'LikedOn'=>$tweet_id));
        }

        public function unLike($user_id,$tweet_id,$get_id){
            $stmt=$this->pdo->prepare("UPDATE tweet SET likesCount=likesCount-1 WHERE tweetID=?");
            $stmt->execute([$tweet_id]);

            $stmt = $this->pdo->prepare("DELETE FROM likes WHERE LikeBy= ? AND LikedOn = ?");
            $stmt->execute(array($user_id,$tweet_id));
        }

        public function likes($user_id,$tweet_id){
            $stmt= $this->pdo->prepare("SELECT * FROM likes WHERE LikeBy= ? AND LikedOn = ?");
            $stmt->execute(array($user_id,$tweet_id));
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getTrendByHash($hashtag){
            $stmt = $this->pdo->prepare("SELECT * FROM trends WHERE hashtag LIKE ? LIMIT 5");
            $stmt->execute([$hashtag.'%']);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }

        public function getMention($mention){
            $stmt=$this->pdo->prepare("SELECT user_id, username, screenName, profileImage FROM users WHERE username LIKE ? OR screenName LIKE ? LIMIT 5");

            $stmt->execute(array($mention.'%',$mention.'%'));
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }

        public function addTrend($hashtag){
            preg_match_all("/#+([a-zA-Z0-9_]+)/i",$hashtag, $matches);

            if($matches){
                $result = array_values($matches[1]);
            }

            $sql = "INSERT INTO trends (hashtag,createdOn) VALUES (?,CURRENT_TIMESTAMP)";

            foreach($result as $trend){
                if($stmt=$this->pdo->prepare($sql)){
                     $stmt->execute(array($trend));
                }
            }
        }

        public function getTweetLinks($tweet){
            $tweet = preg_replace("/(https?:\/\/)([\w]+.)([\w\.]+)/","<a href='$0' target='_blank'>$0</a>",$tweet);
            $tweet = preg_replace("/#([\w]+)/","<a href='".BASE_URL."hashtag/$1'>$0</a>",$tweet);
            $tweet = preg_replace("/@([\w]+)/","<a href='".BASE_URL."$1'>$0</a>",$tweet);

            return $tweet;
        }


        public function getPopupTweet($tweet_id){
            $stmt = $this->pdo->prepare("SELECT * FROM tweet,users WHERE tweetID=? AND tweetBy=user_id");
            $stmt->execute([$tweet_id]);
           
            return $stmt->fetch(PDO::FETCH_OBJ);
        }

        public function retweet($tweet_id, $user_id,$get_id,$comment){
            $stmt= $this->pdo->prepare("UPDATE tweet SET retweetCount=retweetCount+1 WHERE tweetID = ?");
            $stmt->execute([$tweet_id]);

            $stmt = $this->pdo->prepare("INSERT INTO tweet (status,tweetBy,tweetImage,retweetID,retweetBy,postedOn,likesCount,retweetCount,retweetMsg) SELECT status,tweetBy,tweetImage,tweetID,?,postedOn,likesCount,retweetCount,? FROM tweet WHERE tweetID=?");

            $stmt->execute(array($user_id,$comment,$tweet_id));
            

        }


        public function checkRetweet($tweet_id,$user_id){
            $stmt=$this->pdo->prepare("SELECT * FROM tweet WHERE retweetID=? AND retweetBy=? OR tweetID = ? AND  retweetBy = ?");
            $stmt->execute(array($tweet_id,$user_id,$tweet_id,$user_id));
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }


        public function comments($tweet_id){
            $stmt = $this->pdo->prepare("SELECT *FROM comments LEFT JOIN users ON commentBy=user_id WHERE commentOn=?");
            $stmt->execute([$tweet_id]);

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }

        public function countTweets($user_id){
            $stmt = $this->pdo->prepare("SELECT COUNT(tweetID) AS totalTweets FROM tweet WHERE tweetBy=? AND retweetBy = '0' OR retweetBy= ?");
            $stmt->execute(array($user_id,$user_id));
            $count = $stmt->fetch(PDO::FETCH_OBJ);
            echo $count->totalTweets;
        }

        public function countLikes($user_id){
            $stmt=$this->pdo->prepare("SELECT COUNT(likeID) AS totalLikes FROM likes WHERE likeBy = ?");
            $stmt->execute([$user_id]);
            $count = $stmt->fetch(PDO::FETCH_OBJ);
            echo $count->totalLikes;
        }
    }

?>