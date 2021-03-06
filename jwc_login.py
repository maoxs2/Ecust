#!/usr/bin/python2
# coding:utf8
"""
教务处登录
http://jwc.Ecust.edu.cn
"""
import requests
from lxml import etree


# stuID = 10142045 #作者学号23333
# stuPW = 10142045
def jwc_login(stuID, stuPW):
    # 第一种方法，[第二种方法已被废弃]
    x = primary_method(stuID, stuPW)
    if x:
        return x
        pass
    pass


def primary_method(stuID, stuPW):
    get_url = "http://202.120.108.14/ecustedu/K_StudentQuery/K_StudentQueryLogin.aspx"
    get = requests.get(get_url)
    root = etree.HTML(get.text)
    viewstate_tag = root.xpath("//*[@id='__VIEWSTATE']")
    viewstate = viewstate_tag[0].attrib['value']
    eventvalidation_tag = root.xpath("//*[@id='__EVENTVALIDATION']")
    eventvalidation = eventvalidation_tag[0].attrib['value']

    url_ggcx_login = "http://202.120.108.14/ecustedu/K_StudentQuery/K_StudentQueryLogin.aspx"
    payload = {
        'TxtStudentId': stuID,
        'TxtPassword': stuPW,
        '__EVENTVALIDATION': eventvalidation,
        '__VIEWSTATE': viewstate,
        'BtnLogin': '登录'}
    r = requests.post(url_ggcx_login, data=payload)
    text = r.text.encode("utf8")
    if validate_login(text):
        return r.cookies
    else:
        return False
    pass


def validate_login(x):
    signal = "您好"
    if x.find(signal) > 0:
        # print 'login success'
        # for i in cookiejar
        # 	print i
        return True
    else:
        # print 'login failed'
        return False
    pass

# jar = requests.cookies.RequestsCookieJar()
# print vars(r.cookies)


# a = urp_login(stuID, stuPW)
# print vars(a)
