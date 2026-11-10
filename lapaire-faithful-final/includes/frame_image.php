<?php
/**
 * Returns an Unsplash image URL that visually matches
 * the frame's color, shape and category.
 */
function frame_image(string $color='', string $shape='', string $category=''): string {

  $color    = strtolower(trim($color));
  $shape    = strtolower(trim($shape));
  $category = strtolower(trim($category));

  // ── Sunglasses ──────────────────────────────────────────────
  if(str_contains($category,'sun')){
    if(str_contains($color,'gold') || str_contains($color,'yellow'))
      return 'https://images.unsplash.com/photo-1509822929063-6b6cfc9b42f2?w=600&q=80';
    if(str_contains($color,'brown') || str_contains($color,'tort'))
      return 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=600&q=80';
    if(str_contains($color,'white') || str_contains($color,'silver'))
      return 'https://images.unsplash.com/photo-1527576539890-dfa815648363?w=600&q=80';
    // default sunglass — black/dark
    return 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=600&q=80';
  }

  // ── Kids ─────────────────────────────────────────────────────
  if(str_contains($category,'kid')){
    if(str_contains($color,'red') || str_contains($color,'pink'))
      return 'https://images.unsplash.com/photo-1622557850710-58d23f35be96?w=600&q=80';
    if(str_contains($color,'blue'))
      return 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=600&q=80';
    return 'https://images.unsplash.com/photo-1622557850710-58d23f35be96?w=600&q=80';
  }

  // ── Blue Light ───────────────────────────────────────────────
  if(str_contains($category,'blue') || str_contains($category,'light')){
    if(str_contains($color,'clear') || str_contains($color,'transparent'))
      return 'https://images.unsplash.com/photo-1625591340248-787d6dc4a78a?w=600&q=80';
    return 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=600&q=80';
  }

  // ── Prescription — by SHAPE first ────────────────────────────
  if(str_contains($shape,'cat') || str_contains($shape,'cat-eye')){
    if(str_contains($color,'gold') || str_contains($color,'yellow'))
      return 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=600&q=80';
    return 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=600&q=80';
  }
  if(str_contains($shape,'oval') || str_contains($shape,'round')){
    if(str_contains($color,'gold') || str_contains($color,'yellow'))
      return 'https://images.unsplash.com/photo-1591076482161-42ce6da69f67?w=600&q=80';
    if(str_contains($color,'brown') || str_contains($color,'tort'))
      return 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600&q=80';
    if(str_contains($color,'silver') || str_contains($color,'grey') || str_contains($color,'gray'))
      return 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=600&q=80';
    // round black/default
    return 'https://images.unsplash.com/photo-1591076482161-42ce6da69f67?w=600&q=80';
  }
  if(str_contains($shape,'square')){
    if(str_contains($color,'black'))
      return 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=600&q=80';
    if(str_contains($color,'brown') || str_contains($color,'tort'))
      return 'https://images.unsplash.com/photo-1512640904849-e9cf81ba72e8?w=600&q=80';
    return 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=600&q=80';
  }
  if(str_contains($shape,'wayfarer') || str_contains($shape,'classic')){
    return 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=600&q=80';
  }

  // ── Rectangle / default — by COLOR ───────────────────────────
  if(str_contains($color,'black'))
    return 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=600&q=80';
  if(str_contains($color,'brown') || str_contains($color,'tort'))
    return 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600&q=80';
  if(str_contains($color,'gold') || str_contains($color,'yellow'))
    return 'https://images.unsplash.com/photo-1591076482161-42ce6da69f67?w=600&q=80';
  if(str_contains($color,'silver') || str_contains($color,'grey') || str_contains($color,'gray'))
    return 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=600&q=80';
  if(str_contains($color,'blue'))
    return 'https://images.unsplash.com/photo-1625591340248-787d6dc4a78a?w=600&q=80';
  if(str_contains($color,'red') || str_contains($color,'pink'))
    return 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=600&q=80';
  if(str_contains($color,'white') || str_contains($color,'clear') || str_contains($color,'transparent'))
    return 'https://images.unsplash.com/photo-1625591340248-787d6dc4a78a?w=600&q=80';

  // ── Absolute fallback ─────────────────────────────────────────
  return 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=600&q=80';
}
