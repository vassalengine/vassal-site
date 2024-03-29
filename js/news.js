const itemTemplate = document.querySelector('#news_item_tmpl');

function makeNewsItem(slug, id, title, month, day, base_url) {
  const item = document.importNode(itemTemplate.content, true);

  const m = item.querySelector('.tmpl-month');
  m.append(month);

  const d = item.querySelector('.tmpl-day');
  d.append(day);

  const a = item.querySelector('.tmpl-link');
  a.setAttribute('href', `${base_url}/${slug}/${id}`);
  a.append(title);

  return item;
}

async function loadNews(news_url, base_item_url, count) {
  const news = await (await fetch(news_url)).json();
  const news_root = document.getElementById('news_root');

  news['topic_list']['topics']
    // Skip the "About" topic
    .filter(t => t['title'] != 'About the News category')
    // Sort newest first
    .sort(
      (a,b) => a['created_at'] > b['created_at'] ? -1 :
               a['created_at'] < b['created_at'] ? 1 : 0)
    // Take the most recent count items
    .slice(0, count)
    .map(t => {
      const date = new Date(t['created_at']);
      news_root.appendChild(makeNewsItem(
        t['slug'],
        t['id'],
        t['title'],
        date.toLocaleString('en', {month: 'short'}),
        date.toLocaleString('en', {day: 'numeric'}),
        base_item_url
      ));
    });
}

const news_url = 'https://forum.vassalengine.org/c/news/17.json';
const base_item_url = 'https://forum.vassalengine.org/t';
const count = 10;

loadNews(news_url, base_item_url, count);
