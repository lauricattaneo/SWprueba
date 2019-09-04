<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caja\SiafcaIntranetBundle\Entity\Organismo;

class ContactoController extends Controller
{
    /**
     * Construye el formulario de contacto a un organismo y renderiza el template. Llama a funcion de enviar mail.
     * @param Request $request
     * @param Organismo $Organismo Organismo al que quiere mandarse una comunicación (Se pasa el id por url)
     * @return
     * @throws \Exception Lanza una excepción si la entidad o el responsable no tienen mail asociado
     * 
     * IntraConInd - indexAction
     */
    public function indexAction(Request $request, Organismo $Organismo)
    {
        // Action is performed by logged users only
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $next = NULL;

        // Get emails of entity and responsable
        $emails = array();
        $emails[] = explode(';', trim($Organismo->getCorreos()));
        $emails[] = explode(';', trim($Organismo->getUsuarioResponsableMail()));
        // Generate a flat array with mails (one dimension)
        $flat_emails = array_filter(call_user_func_array('array_merge', $emails));

        // If there are no emails, exit with error
        if (count($flat_emails) === 0) { throw new \Exception('No hay emails asociados a esta organización'); }

        // Generate form with general object with default info (StdClass)
        // The ContactoType is expecting an StdClass element with all properties
        $values = (object) array(
            // Default values:
            'orgName' => $Organismo->getNombre(), // Default value, not modified by form submit
            'defaultEmails' => $flat_emails, // Default value, not modified by form submit
            // Submitted values:
            'contactFirstName' => '',
            'contactLastName' => '',
            'contactMail' => '',
            'contactPhone' => '',
            'destinationEmails' => '',
            'subject' => '',
            'message' => '',
            
        );
        $contactoForm = $this->createForm('Caja\SiafcaIntranetBundle\Form\ContactoType', $values);

        $contactoForm->handleRequest($request);
        
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraConIndex'));
        
        if ($contactoForm->isSubmitted() && $contactoForm->isValid()) {
            $data = $contactoForm->getData();
            $sent = $this->enviarMail(
                $data->destinationEmails,
                $data->subject,
                $data->message,
                $data->contactFirstName,
                $data->contactLastName,
                $data->contactPhone,
                $data->contactMail);

            // If mail was successfully sent, alert user and reset form
            if ($sent) {
                $this->addFlash(
                    'success', // error, info, success
                    'El mensaje fue enviado correctamente'
                );

                $next = $this->redirect($this->generateUrl('organismo_index'));
            } else {
                $this->addFlash(
                    'error', // error, info, success
                    'Ha ocurrido un problema. Verifique los datos o intente nuevamente más tarde'
                );
            }
        }

        if (!$next) {
            $next = $this->render('contacto/index.html.twig', array(
                'titulo' => 'Contacto',
                'form' => $contactoForm->createView(),
                'ayuda' => $ayuda, 
                ));
        }

        return $next;
    }

    /**
     * Función que envia un mail a una organización. Los datos del usuario emisor se utilizan para
     * proporcionar información de contacto en la firma del mensaje. (nombre, apellido, telefono)
     * @param String $customEmails Emails de los destinatarios. Si son varios estan separados por ';' (Insertados por el usuario en un formulario)
     * @param String $subject Asunto del mensaje
     * @param String $message Mensaje a entregar
     * @param String $contactFirstName Nombre (Opcional) del emisor del mensaje (Se agrega a firma)
     * @param String $contactLastName Apellido (Opcional) del emisor del mensaje (Se agrega a firma)
     * @param String $contactPhone Telefono (Opcional) de contacto al emisor del mensaje (Se agrega a firma)
     * @param String $contactMail Mail (Opcional) de contacto al emisor del mensaje (Se agrega a firma)
     * @return boolean Retorna true si el mail fue enviado sin errores, de lo contrario false.
     */
    public function enviarMail($customEmails, $subject, $message, $contactFirstName = NULL, $contactLastName = NULL, $contactPhone = NULL, $contactMail = NULL)
    {
        $to = array_map('trim', array_filter(explode(';', trim($customEmails))));
        $body = $this->paragraphsWrap($message);
        
        // If there is contact info, add it to bottom of body
        if (trim($contactFirstName.$contactLastName) !== '' || ($contactPhone || $contactMail)) {
            $signature = '<div>---------------------------<br />Ponerse en contacto con:<br />'
                        .$contactFirstName.' '.$contactLastName
                        .(($contactPhone)? '<br />Tel.: <a href="tel:'.$contactPhone.'">'.$contactPhone.'</a>' : '');
            foreach (array_map('trim', array_filter(explode(';', trim($contactMail)))) as $singleMail) {
                $signature .= ((trim($singleMail) !== '')? '<br />Email.: <a href="mailto:'.trim($singleMail).'">'.trim($singleMail).'</a>' : '');
            }
            $signature .= '</div>';
            $body .= $signature;
        }

        $from = array('siafca@dev.org' => 'Siafca Dev');

        try {
            $mail = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($from)
                ->setTo($to)
                ->setReadReceiptTo($from)
                ->setBody($body, 'text/html');

            $success = $this->get('mailer')->send( $mail );
        } catch (\Exception $e) {
            //throw new \Exception('Error al enviar mail');
            $success = false;
        }

        return $success;
    }

    /**
     * Transforma el contenido de textarea para mostrarlo correctamente en emails
     * @param String $body Cuerpo de email
     * @return string Cuerpo de email separado en parrafos
     */
    function paragraphsWrap($body)
    {
        $array = explode("\n", $body);
        $paragraphs = "";
        foreach($array as $paragraph) {
            $paragraphs .= '<p>'.wordwrap($paragraph, 60, '<br />').'</p>';
        }
        return $paragraphs;
    }
}
