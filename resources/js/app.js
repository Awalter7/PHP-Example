import './bootstrap';
import React from 'react'
import { createRoot } from 'react-dom/client'
import MovieSlider from './components/MovieSlider'

document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('movie-slider')
  if (!el) return
  
  const movies = JSON.parse(el.dataset.movies)
  const code = el.dataset.code;
  const currentName = el.dataset.currentName;
  const sessionId = el.dataset.sessionid;

  createRoot(el).render(
    <MovieSlider 
      movies={movies} 
      code={code} 
      currentName={currentName} 
      sessionId={sessionId}
    />
  );
})