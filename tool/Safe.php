<?php
/**
 * Safe
 *
 * @package ko\tool
 * @author zhangchu
 */

class Ko_Tool_Safe
{
	/**
	 * 通过设置 POST 时允许的 ref 域名来保证基本的安全
	 *
	 * @param array $aPostAllowRefDomain 只允许ref为同样的域名 array(),
	 *                                   允许ref为任意域名 array('*'),
	 *                                   允许ref为指定某些域名 array('*.test.com', 'www.demo.com'),
	 *                                   ref为空被视为可以访问不进行这些检查
	 */
	public static function BCheckMethod($aPostAllowRefDomain = array())
	{
		if ('POST' === getenv('REQUEST_METHOD'))
		{
			$referer = getenv('HTTP_REFERER');
			if (strlen($referer))
			{
				$refinfo = parse_url(strtolower($referer));
				if (empty($aPostAllowRefDomain))
				{
					$servername = getenv('SERVER_NAME');
					if ($refinfo['host'] !== $servername)
					{
						return false;
					}
				}
				else
				{
					if (!self::_BCheckDomains($refinfo['host'], $aPostAllowRefDomain))
					{
						return false;
					}
				}
			}
		}
		return true;
	}
	
	private static function _BCheckDomains($sDomain, $aAllowDomain)
	{
		foreach ($aAllowDomain as $allowDomain)
		{
			if (self::_BCheckDomain($sDomain, $allowDomain))
			{
				return true;
			}
		}
		return false;
	}
	
	private static function _BCheckDomain($sDomain, $sAllowDomain)
	{
		if ('*' === $sAllowDomain)
		{
			return true;
		}
		if ('*.' === substr($sAllowDomain, 0, 2))
		{
			$rootDomain = substr($sAllowDomain, 2);
			if ($sDomain === $rootDomain
				|| substr($sDomain, -1-strlen($rootDomain)) === '.'.$rootDomain)
			{
				return true;
			}
		}
		return $sDomain === $sAllowDomain;
	}
}
