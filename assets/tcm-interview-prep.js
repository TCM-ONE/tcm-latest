(function () {
  'use strict';

  var search = document.getElementById('questionSearch');
  var topic = document.getElementById('topicFilter');
  var level = document.getElementById('levelFilter');
  var clear = document.getElementById('clearFilters');
  var result = document.getElementById('questionResult');
  var noResults = document.getElementById('questionNoResults');
  if (!search || !topic || !level || !clear || !result || !noResults) return;

  var cards = Array.prototype.slice.call(document.querySelectorAll('[data-question-card]'));
  var sections = Array.prototype.slice.call(document.querySelectorAll('[data-topic-section]'));
  var total = cards.length;

  function applyFilters() {
    var phrase = search.value.trim().toLocaleLowerCase();
    var selectedTopic = topic.value;
    var selectedLevel = level.value;
    var visible = 0;

    cards.forEach(function (card) {
      var matchesPhrase = !phrase || (card.getAttribute('data-search') || '').toLocaleLowerCase().indexOf(phrase) !== -1;
      var matchesTopic = selectedTopic === 'all' || card.getAttribute('data-topic') === selectedTopic;
      var matchesLevel = selectedLevel === 'all' || card.getAttribute('data-level') === selectedLevel;
      var show = matchesPhrase && matchesTopic && matchesLevel;
      card.hidden = !show;
      if (show) visible++;
    });

    sections.forEach(function (section) {
      var count = section.querySelectorAll('[data-question-card]:not([hidden])').length;
      section.hidden = count === 0;
      var empty = section.querySelector('[data-topic-empty]');
      if (empty) empty.hidden = true;
    });

    result.textContent = visible + (visible === 1 ? ' question shown' : ' questions shown') + ' of ' + total;
    noResults.hidden = visible !== 0;
  }

  search.addEventListener('input', applyFilters);
  topic.addEventListener('change', applyFilters);
  level.addEventListener('change', applyFilters);
  clear.addEventListener('click', function () {
    search.value = '';
    topic.value = 'all';
    level.value = 'all';
    applyFilters();
    search.focus();
  });
}());
