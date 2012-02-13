import dbus
import json
import urllib
import socket

NM = 'org.freedesktop.NetworkManager'
NMP = '/org/freedesktop/NetworkManager'
NMI = NM + '.Device'
PI = 'org.freedesktop.DBus.Properties'

def list_ssids(): 
    bus = dbus.SystemBus()
    nm = bus.get_object(NM,NMP)
    nmi = dbus.Interface(nm,NM)
    # Iterate over the devices queried via the interface
    for dev in nmi.GetDevices():
        # get each and bind a property interface to it
        devo = bus.get_object(NM,dev)
        devpi = dbus.Interface(devo,PI)
        if devpi.Get(NM+'.Device','DeviceType') == 2:
            wdevi = dbus.Interface(devo,NMI + '.Wireless')
            wifi = []
            for ap in wdevi.GetAccessPoints():
                apo = bus.get_object(NM,ap)
                api = dbus.Interface(apo,PI)
                wifi.append({'ssid':''.join(["%c" % b for b in api.Get("org.freedesktop.NetworkManager.AccessPoint", "Ssid", byte_arrays=True)]), 'mac':''.join(["%c" % b for b in api.Get("org.freedesktop.NetworkManager.AccessPoint", "HwAddress", byte_arrays=True)]), 'laptopid': socket.gethostname()})           
    return wifi

if __name__ == '__main__':
  ap = list_ssids()
  
  data = json.dumps(ap)
  params = urllib.urlencode({'wifi': data})
  f = urllib.urlopen("http://127.0.0.1/laptoptracker/server/geopost2.php", params)
