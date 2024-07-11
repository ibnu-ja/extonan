import { siBitbucket, siFacebook, siGithub, siGitlab, siGoogle, siLinkedin, siSlack, siX } from 'simple-icons'

export function useProvider() {
  const providerIcons = [
    {
      id: 'bitbucket',
      icon: siBitbucket.path,
    },
    {
      id: 'facebook',
      icon: siFacebook.path,
    },
    {
      id: 'github',
      icon: siGithub.path,
    },
    {
      id: 'gitlab',
      icon: siGitlab.path,
    },
    {
      id: 'google',
      icon: siGoogle.path,
    },
    {
      id: 'facebook',
      icon: siFacebook.path,
    },
    {
      id: 'linkedin',
      icon: siLinkedin.path,
    },
    {
      id: 'linkedin-openid',
      icon: siLinkedin.path,
    },
    {
      id: 'slack',
      icon: siSlack.path,
    },
    {
      id: 'twitter',
      icon: siX.path,
    },
    {
      id: 'twitter-oauth-2',
      icon: siX.path,
    },
  ]
  function getIcon(id: string) {
    const item = providerIcons.find(item => item.id == id)
    return item?.icon
  }
  return { getIcon }
}
