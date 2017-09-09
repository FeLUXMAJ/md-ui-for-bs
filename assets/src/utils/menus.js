const mapping = {
  '皮肤工具': {
    title: 'plugins.skinUtilities.menu',
    icon: 'card_travel',
    link: '/user/skin-utilities'
  },
  '我的举报': {
    title: 'plugins.reportTexture.userSide',
    icon: 'feedback',
    link: '/user/report'
  },
  'Blessing\\ConfigGenerator::config.generate-config': {
    title: 'plugins.configGenerator.title',
    icon: 'tune',
    link: '/user/config'
  }
}

function externalLink (link) {
  return `/go?dst=${encodeURI(link)}`
}

export default ({ title, link }) => {
  return title in mapping
    ? mapping[title]
    : {
      title,
      icon: 'polymer',
      link: externalLink(link)
    }
}
