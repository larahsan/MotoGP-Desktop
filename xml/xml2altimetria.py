import xml.etree.ElementTree as ET

class Svg(object):

    def __init__(self):
        self.raiz_svg = ET.Element('svg', 
                                   xmlns="http://www.w3.org/2000/svg",
                                   version="2.0",
                                   viewBox="0 0 500 160")

    def addPolyline(self,points,stroke,stroke_width,fill):
        ET.SubElement(self.raiz_svg,'polyline',
                      points=points,
                      stroke=stroke,
                      **{'stroke-width': str(stroke_width)},
                      fill=fill)

    def obtenerCoordenadas(self):
        arbol = ET.parse('xml/circuitoEsquema.xml')
        raiz = arbol.getroot()
        ns = '{http://www.uniovi.es}'

        coordenadas = [] # Coordenadas de distancia y altitud

        for tramo in raiz.findall(f'.//{ns}tramos/{ns}tramo'):
            distancia = float(tramo.find(f'.//{ns}distancia').text)
            altitud = float(tramo.find(f'{ns}coordenadas').get("altitud"))
            coordenadas.append((distancia, altitud))

        return coordenadas
    
    def crearSvg(self, coordenadas):
        puntos = []
        x = 10 # Para que haya un poco de margen  
        
        # Coordenada inicial en el eje X
        puntos.append(f"{x},160")

        for dist, alt in coordenadas:
            x += dist / 10  # Escalar distancia
            y = 160 - (alt - 300) * 4  # Escalar altitud
            puntos.append(f"{x},{y}")
        
        # Añadir el último punto para cerrar la polilínea al suelo
        puntos.append(f"{x},160")
        puntos.append("10,160")

        # Corregir el formato
        puntos = " ".join(puntos)
        
        return puntos

    def escribir(self, nombreArchivoSVG):
        arbol = ET.ElementTree(self.raiz_svg)
        ET.indent(arbol)
        arbol.write(nombreArchivoSVG, encoding='utf-8', xml_declaration=True)

def main():
    svg = Svg()
    coordenadas = svg.obtenerCoordenadas()
    puntos = svg.crearSvg(coordenadas)
    svg.addPolyline(puntos, "#0c2d48", "4", "none")
    svg.escribir("xml/altimetria.svg")

if __name__ == "__main__":
    main()
