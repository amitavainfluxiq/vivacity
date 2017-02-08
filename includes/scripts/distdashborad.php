<?php
global $AI;
require_once(ai_cascadepath('includes/plugins/landing_pages/class.landing_pages.php'));
require_once(ai_cascadepath('includes/modules/mlmsignup/class.enrollment_lp.php'));

require_once(ai_cascadepath('includes/modules/genealogy/class.genealogy.php'));

$gene            = new C_genealogy( $AI->get_setting('structure_show_genealogy') ? C_genealogy::GENEALOGY_TREE : C_genealogy::ENROLLER_TREE );
$is_logged_in    = $AI->user->isLoggedIn();
$is_admin        = $AI->get_access_group_perm('Administrators');
$is_in_genealogy = $is_logged_in ? $gene->is_descendant(AI_STRUCTURE_NODE_ROOT, util_affiliate_id()) : false;

$userId = $AI->user->userID;
$emailid = $AI->user->email;



$res12 = db_query("SELECT * FROM `users` WHERE `userID` = ".$userId);

while($res12 && $row12 = db_fetch_assoc($res12)) {
    $accept_term = $row12['accept_term'];
}

if($accept_term == 0) {


    if (count($_POST)) {
        $userId = $AI->user->userID;
        $util_rep_id = 100;

        $res = db_query("SELECT * FROM `users` WHERE `userID` = " . $userId);

        while ($res && $row = db_fetch_assoc($res)) {
            $parent = $row['parent'];

            if ($parent) {
                $util_rep_id = $parent;
            }
        }

        // add user at geneology tree

        $gene = new C_genealogy(C_genealogy::GENEALOGY_TREE);
        try {
            $gene->insert_node($userId, $util_rep_id, null, 'stop', true);
        } catch (NodeAlreadyInTreeException $naite) {
            $data = $naite->get_data();
            if ($data['parent'] != $util_rep_id) {
                $gene->move_sub_tree($userId, $util_rep_id, 0);
            }
        }

        // add user at enrollment tree

        $gene = new C_genealogy(C_genealogy::ENROLLER_TREE);
        try {
            $gene->insert_node($userId, $util_rep_id, null, 'stop', true);
        } catch (NodeAlreadyInTreeException $naite) {
            $data = $naite->get_data();
            if ($data['parent'] != $util_rep_id) {
                $gene->move_sub_tree($userId, $util_rep_id, 0);
            }
        }


        db_query("UPDATE `users` SET `accept_term`=1 WHERE `userID` = " . $userId);


        $email_name = 'Accept terms';
        $send_to = $emailid;
        $send_from = 'iftekarkta@gmail.com';

        $vars = array();
        //$vars['name'] = 'Samsuj Jaman';
        $vars['name'] = $AI->user->username;

        $defaults = array();
        $defaults['email_subject'] = ' $se->send email';
        $defaults['email_msg'] = getmailbody($AI->user->username);

        $se = new C_system_emails($email_name);
        $se->set_from($send_from);
        $se->set_defaults_array($defaults);
        $se->set_vars_array($vars);
        $se->send($send_to);

        util_redirect('dashboard');

    }




    ?>

    <script>
        function validate() {
            if ($('#iagreetoit').is(':checked')) {
                return true;
            } else {
                alert('Please accept terms and conditions.');
                return false;
            }
        }
    </script>

    <div class="dl_content_block">

    <form action="" method="post" onsubmit="return validate()">
        <div>
            <h2>Promoter Agreement</h2>
 <!--           <textarea>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.

    It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).

    Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.

</textarea>-->

            <div style="height: 500px; overflow: auto; border: 1px solid #c0c3c5; background: #ffffff; padding: 15px; font-size: 14px;">
                December 1st, 2016<br><br>
                <span style="color: #537518; font-weight: bold;">INTRODUCTION</span><br><br><br>
                <div style="line-height: 30px;">Your participation as a Vivacity Promoter is very important to us, please take the time to review these terms since they affect you and how your Vivacity, a MAKEWAY Wellness Company, business may be run. If you have any questions or comments about these Policies and Procedures, please email customerservice@vivacitygo.com<br><br>
                Policies and Procedures Incorporated into Promoter Agreement:<br><br>
                These Policies and Procedures are a key part of the Vivacity, a MAKEWAY Wellness Company, Agreement. They may be amended or altered at any time without prior notice at the discretion of Vivacity. Hereinafter referred to as “Vivacity” or the “Company”.<br><br>
                Throughout these Policies, when the term Agreement is used, it refers to the VIVACITY Promoter Application and Agreement, Promoter Terms and Conditions, and these Policies and Procedures. These documents are incorporated by reference into the VIVACITY Promoter Agreement (all in their current form and as amended by VIVACITY).<br><br>
                It is your responsibility as a Promoter to read, understand, and follow the most current version of these Policies and Procedures. When sponsoring or enrolling a new Promoter, the sponsoring Promoter must provide the most current version of the Terms and Conditions and these Policies and Procedures to the prospect prior to his or her execution of the Promoter Agreement.<br><br>
                By enrolling as a Promoter, the Applicant acknowledges that all of these Policies and Procedures have been read, completely understood and accepted as valid and binding between themselves and the Company.
                    </div><br><br>
                <span style="color: #537518; font-weight: bold;">Purpose of Policies and Procedures</span><br><br>
                <div style="line-height: 30px;">Vivacity is a direct sales company that markets products through you, our Independent Promoters. Your success and the success of your fellow Promoters are dependent upon the integrity of the men and women who market our products.<br><br>
                Vivacity Promoters are required to comply with the Agreement which VIVACITY may amend at its sole discretion from time to time, as well as all federal, state, and local laws governing their VIVACITY business and their conduct. Because you may be unfamiliar with many of these standards of practice, it is very important that you read and abide by the Agreement.<br><br>
                Please review the information in this manual carefully. This Agreement was created to explain and govern the relationship you, as an independent contractor and Promoter and VIVACITY. It also sets a standard for acceptable business conduct between both VIVACITY and you, and you and your community.<br><br>
                If you have any questions regarding any policy or rule, contact customerservice@vivacitygo.com.<br>
                The Company honors all general, state and local regulations governing network marketing and you are required to do the same.<br><br>
                The Company reserves the right to employ such measures as are deemed necessary to determine and ensure compliance with its Policies and Procedures.
                    </div><br><br>
                <span style="color: #537518; font-weight: bold;">Code of Ethics:</span><br><br>
                <div style="line-height: 30px;">You, as a Promoter, agree to conduct your business according to the following Code of Ethics, which are an integral part of these Policies and Procedures. This code ensures standards of professionalism and integrity throughout the Company’s network of Independent Promoters and protects the business image of the Company Promoter as well as the overall image of the Company.<br><br>
                As a VIVACITY Independent Promoter, I agree to:<br><br>
                Be honest and fair and deal with customers and other Promoters with the highest standards of honesty, integrity, and fairness.</div><br><br>


                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;">Represent the Company’s services completely according to the literature, without making misleading sales claims.<br></li>
                    <li style="line-height: 30px;">Represent the Company’s financial plan completely and without exaggeration to all potential Promoters.<br></li>
                    <li style="line-height: 30px;">Follow through with all obligations associated with sponsoring other Promoters, including training, motivation and support.<br></li>
                    <li style="line-height: 30px;">Become familiar with and abide by the Company’s Terms and Conditions of Promoter, Policies and Procedures of Promoters, VIVACITY Terms of Service, and the Web Privacy Policy.<br></li>
                    <li style="line-height: 30px;">Become familiar with and abide by local, state and federal statutes.<br></li>
                    <li style="line-height: 30px;">Be solely responsible for all financial and/or legal obligations incurred in the course of my business as a Promoter of the Company.<br></li>
                </ul>

                <br>

                <span style="color: #537518; font-weight: bold;">How to Become a Promoter:</span> <br><br>


                To become a Promoter, please follow the following steps:<br><br>


                <ol style="list-style: inside none inside; padding-left: 20px;">
                    Please have available the following information:
                        <br>
                    <li style="line-height: 30px;">Your name or business name, address, phone number, Social Security Number or Federal Tax Identification Number, and your Sponsor’s ID#.<br></li>
                    <li style="line-height: 30px;">Complete the On-line Promoter Application, found on your Sponsor’s Web Site.</li>
                    <li style="line-height: 30px;">To be eligible to participate in our Compensation Plan, and/or have an Promoter Web Site, all Promoters must pay an Promoter Enrollment Fee of $29.95. Any additional purchases of products, promotional materials, or sales materials are strictly optional.<br></li>
                    <li style="line-height: 30px;">VIVACITY accepts MasterCard, Visa, and American Express, Visa or MasterCard debit cards, or a money order mailed to VIVACITY. All enrollments and product sales are contingent on VIVACITY receiving payment on the order.<br></li>
                    <li style="line-height: 30px;">At the end of the enrollment process, you will receive a Promoter ID Number. This number will be necessary whenever a Promoter wants to order products, use our personalized web services, request information regarding orders or sales, sign up new Promoters and use other VIVACITY services.<br></li>
                    <li style="line-height: 30px;">Remember, once you enroll, you are agreeing to the Terms and Conditions of being a Promoter, and to comply and be bound by the Policies and Procedures. The Applicant and VIVACITY have entered into a binding contract on those terms to which both are obligated to comply and adhere.<br></li>
                </ol>
                <br><br>
                <div style="line-height: 30px;">Please be sure to read and review the Terms and Conditions of Promotership and the Policies and Procedures. Any new Promoter has accepted the Terms and Conditions and Policies and Procedures and is bound by their provisions.</div><br><br>
                Remember, to become an Independent Promoter, each applicant must:<br><br>


                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;">Be of legal age in the state in which he or she resides.</li>
                    <li style="line-height: 30px;">Be a resident of the United States, U.S. Territories.</li>
                    <li style="line-height: 30px;">Have a valid Social Security or Federal Tax ID number.</li>
                    <li style="line-height: 30px;">Read, understand and agree to the Company’s Policies and Procedures and the Terms and Conditions of the Promoter Agreement.</li>
                    <li style="line-height: 30px;">Submit a properly completed and signed Promoter Application and Agreement or a complete on-line Promoter Application to VIVACITY.</li>
                    <li style="line-height: 30px;"> Be accepted by the Company and pay the Promoter Enrollment Fee</li>
                </ul>
                <br><br>
                VIVACITY reserves the right to reject any applications for a new Promoter or applications for renewal.<br><br>
                <span style="color: #537518; font-weight: bold;">Your Rights as a Promoter:</span><br><br>
                Promoters are authorized to sell the Company’s products and to participate in the Compensation Plan.<br><br>
                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;">Promoters may sponsor new Promoters to sell the Company’s products.</li>
                    <li style="line-height: 30px;">Independent Promoters may sell our products, with no territorial restrictions, in all countries where the Company has authorized the sale of its products.</li>
                    <li style="line-height: 30px;">There are no franchise or territorial restrictions on a Promoter with regard to sale, promotion or marketing of products and no franchise fees are required.</li>
                    <li style="line-height: 30px;">Promoters have the right to conduct business anywhere in the United States of America, without exclusivity.</li>
                </ul>

                <br><br>

                <span style="color: #537518; font-weight: bold;">Vivacity Business Policies</span><br><br>

                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Promoter as an Individual</span> — The Company will recognize individuals as Independent Promoters. The enrolled Applicant shall be designated as the Independent Promoter.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Assumed Names</span> — A person or entity may apply under a legally registered assumed name (DBA), provided that the Applicant provides a contact name at the time of enrolling.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Multiple Promoterships</span> — A Promoter may operate or have an ownership interest, legal or equitable, as a sole proprietorship, partner, shareholder, trustee, or beneficiary, in only one VIVACITY business.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Promoter Individuals</span> — If any individual (including spouses) Promoter in any way with a corporation, partnership, trust or other entity (collectively Promoter individual) violates the Agreement, such action(s) will be deemed a violation by the entity, and VIVACITY may take disciplinary action against the entity.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Addition of Co-Applicants</span> — When adding a co-applicant (either an individual or a business entity) to an existing VIVACITY business, the Company requires both a written request as well as a properly completed Promoter Application and Agreement containing the applicant and co-applicant’s Social Security Numbers and signatures. To prevent the circumvention of Section 20 (regarding transfers and assignments of VIVACITY business), the original applicant must remain as a party to the original Promoter Application and Agreement. If the original Promoter wants to terminate his or her relationship with the Company, he or she must transfer or assign his or her business in accordance with Section 20. If this process is not followed, the business shall be canceled upon the withdrawal of the original Promoter. All bonus and commission checks will be sent to the address of record of the original Promoter. Please note that the modifications permitted within the scope of this paragraph do not include a change of sponsorship. Changes of sponsorship are addressed in Section 41, below. There is a $100.00 fee for each change requested, which must be included with the written request and the completed Promoter Application and Agreement. VIVACITY may, at its discretion, require notarized documents before implementing any changes to a VIVACITY business. Please allow thirty (30) days after the receipt of the request by VIVACITY for processing.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Promoter Number</span> — Each Promoter will be assigned a Promoter ID Number. This number must be used when ordering and corresponding with the Company.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Change of Address</span> — Promoters agree to report any change of mailing or e-mail address and/or telephone number(s) as soon as possible. Such changes can be made through the Internet at any time at customerservice@vivacitygo.com or by calling Customer Service at 800.928.9401 .</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Changes to Promoter’s Business</span> — Each Promoter must immediately notify VIVACITY of all changes to the information contained on his or her Promoter Agreement. Promoters may modify their existing Promoter Agreement (i.e., change Social Security number to Federal I.D. number, or change the form of ownership from an individual proprietorship to a business entity owned by the Promoter) by submitting a written request, a properly executed Promoter Agreement, and appropriate supporting documentation.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Annual Renewal</span> — The term of the Promoter Agreement is one year from the date of its acceptance by VIVACITY. All Promoters must renew their Promoter status on an annual basis at a cost of $29.95. The renewal amount is due on or before the anniversary month the Promoter entered the program. Failure to renew in a timely manner will mean the loss by the Promoter of all rights, his/her removal from the Promoter Network, forfeiture of all unpaid overrides, bonuses and/or commissions, and loss of the Promoter’s personal group or organization.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Renewal Procedure</span> — If a Promoter is not renewed within 30 days after the anniversary month, the Promoter will be “Suspended.” All commissions, overrides and bonuses will be accumulated and held by the Company until the renewal is received. If the Promoter is not renewed by 60 days after the anniversary month, the Promoter will be terminated permanently and no commissions, overrides, and/or bonuses will be paid. Once terminated, the Promoter can reapply as a new Promoter after waiting six (6) months.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Business Records</span> — Promoters understand that income and profits are dependent upon the sale of products, and agree to maintain necessary business records regarding said products, including sales receipts. Further, Promoter agrees to complete, execute and file any and all reports and other forms required by any law or public authority with respect to the sale of the Company products and shall at all times abide by any and all federal, state or municipal laws and regulations which may be applicable.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Independent Contractor Status</span> — Promoters are independent contractors, and are not purchasers of a franchise or a business opportunity. The agreement between VIVACITY and its Promoters does not create an employer/employee relationship, agency, partnership, or joint venture between the Company and the Promoter. Promoters shall not be treated as an employee for their services or for Federal or State tax purposes. All Promoters are responsible for paying local, state, and federal taxes due from all compensation earned as a Promoter of the Company. The Promoter has no authority (expressed or implied), to bind the company to any obligation, and VIVACITY disclaims any liability for their actions in any respect. Each Promoter shall establish his or her own goals, hours, and methods of sale, so long as he or she complies with the terms of the Promoter Agreement, these Policies and Procedures, and applicable laws.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Minors</span> — A person who is recognized as a minor in his/her state of residence may not be a VIVACITY Promoter. Promoters shall not enroll or recruit minors into the VIVACITY program. Guardianships are considered a Business Entity, and must be documented by completing a VIVACITY Business Entity Registration Form with appropriate documentation attached.</li>
                </ul>

                <br><br>

                <span style="color: #537518; font-weight: bold;">Business Entities</span><br><br>

                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Partnerships</span> — An Applicant wishing to do business under a Partnership must list the complete Partnership name as the designated Applicant. In addition, the Federal Tax Identification Number of the Partnership, a completed VIVACITY Business Entity Registration and the name of the partner who will be the responsible party for the Partnership must be provided.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Corporations</span> — Corporations wishing to be independent Promoters must provide the Federal Tax Identification Number of the Corporation, a completed VIVACITY Business Entity Registration and the name of the individual who will be the contact for the Corporation. In addition, a copy of the Charter or Articles of Incorporation, copies of Minutes authorizing the Corporation to be an Independent Promoter, a list of all officers and shareholders with names, addresses and telephone numbers of each, and the name of an individual as a Corporate representative who shall be the party to contact on behalf of the Independent Promoter’s Promotership must be sent to the Company with the newly-assigned Promoter ID Number attached.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Corporate and Partnership Guarantee for Owners</span> — Although the Company has offered Promoter the opportunity to conduct their Promoter Promoterships as Corporate or Partnership entities, it is agreed that since the Promoter’s Promotership entity is under the control of its owners or principals, the actions of individual owners as they may affect the Company and Promoter’s Promotership are critical to the Company’s business. Therefore, it is agreed that the actions of corporate shareholders, officers, directors, agents, or employees, which are in contravention to the Company’s policies, shall be attributable to the Corporate or Partnership entity. In addition to the foregoing, a properly completed VIVACITY Business Entity Registration must be submitted to VIVACITY. The Business Entity Registration must be signed by all of the shareholders or partners. All shareholders and all partners, as the case may be, are jointly and severally liable for any indebtedness or other obligations to VIVACITY arising out of the operation of VIVACITY Promotership by their corporation or partnership.</li>
                </ul>
            <br>
                <span style="color: #537518; font-weight: bold;">Marriage and Promoter Promoterships</span><br><br>

                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;">Married Promoters’ Spouses (including common law spouses) are considered to be one Promotership, and, accordingly, one spouse may not sponsor the other. If either spouse is enrolled as a Promoter, the couple is considered to be one Promotership.</li>
                    <li style="line-height: 30px;">Existing Promoters who Choose To Marry — If existing unmarried Promoters decide to marry, each of their Promoterships may remain intact and in their existing downlines.</li>
                    <li style="line-height: 30px;">Separation of a VIVACITY Business — VIVACITY Promoters sometimes operate their VIVACITY businesses as husband-wife partnerships, regular partnerships or corporations. At such time as a marriage may end in divorce or a corporation or partnership (the latter two entities are collectively referred to herein as, entities.) may dissolve, arrangements must be made to assure that any separation or division of the business is accomplished so as not to adversely affect the interests and income of other businesses up or down the line of sponsorship. If the separating parties fail to provide for the best interests of other Promoters and the Company, VIVACITY will involuntarily terminate the Promoter Agreement and roll-up their entire organization pursuant to Section 37.  <span style="color: #537518; font-weight: bold;">During the pendency of a divorce or entity dissolution, the parties must adopt one of the following methods of operation:</span>


                        <ul style="list-style: inside; padding-left: 50px;">
                            <li style="line-height: 30px;">One of the parties may, with consent of the other(s), operate the VIVACITY business pursuant to an assignment in writing whereby the relinquishing spouse, shareholders, partners or trustees authorize VIVACITY to deal directly and solely with the other spouse or non-relinquishing shareholder, partner or trustee.</li>
                            <li style="line-height: 30px;">The parties may continue to operate the VIVACITY business jointly on a business-as-usual. basis, whereupon all compensation paid by VIVACITY will be paid in the joint names of the Promoters or in the name of the entity to be divided as the parties may independently agree between themselves.</li>
                        </ul>


                    </li>
                </ul>

                <br><br>
                <div style="line-height: 30px;">Similarly, under no circumstances will VIVACITY split commission and bonus checks between divorcing spouses or members of dissolving entities. VIVACITY will recognize only one downline marketing organization and will issue only one commission check per VIVACITY business per commission cycle. Commission checks shall always be issued to the same individual or entity. In the event that parties to a divorce or dissolution proceeding are unable to resolve a dispute over the disposition of commissions and ownership of the business, the Promoter Agreement shall be involuntarily canceled.<br><br>
                If a former spouse or a former entity Promoter has completely relinquished all rights in their original VIVACITY business, they are thereafter free to enroll under any sponsor of their choosing, so long as they meet the waiting period requirements outlined below. In such case, however, the former spouse or partner shall have no rights to any Promoters in their former organization or to any former retail customer. They must develop the new business in the same manner as any other new Promoter.<br><br>
                <span style="color: #537518; font-weight: bold;">Sales, Transfers and Succession of Promoter Promoterships</span>
                    </div><br><br>

                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Sale, Transfer or Assignment</span> — A Promoter may not be transferred, assigned, or sold in whole or in part without the prior written approval of the Company. As a condition of the sale or transfer, all parties must provide a statement indicating the terms of the proposed sale or transfer and their agreement, along with notarized signatures and payment of $100 to cover the cost of the transfer. <span style="color: #537518; font-weight: bold;">If an Promoter wishes to sell his or her VIVACITY business, the following criteria must be met:</span>
                        <ul style="list-style: inside; padding-left: 50px;">
                            <li style="line-height: 30px;">Protection of the existing line of sponsorship must always be maintained so that the VIVACITY business continues to be operated in that line of sponsorship.</li>
                            <li style="line-height: 30px;">The buyer or transferee must be (or must become) a qualified VIVACITY Promoter. If the buyer is an active VIVACITY Promoter, he or she must first terminate his or her VIVACITY business simultaneously with the purchase, transfer, assignment or acquisition of any interest in the new VIVACITY business.</li>
                            <li style="line-height: 30px;">Before the sale, transfer or assignment can be finalized and approved by VIVACITY. Any debt obligations the selling Promoter has with VIVACITY must be satisfied.</li>
                            <li style="line-height: 30px;">The selling Promoter must be in good standing and not in violation of any of the terms of the Agreement in order to be eligible to sell, transfer or assign a VIVACITY business.</li>
                        </ul>
                        <br><br>
                        Prior to selling a VIVACITY business, the selling Promoter must notify VIVACITY’s Promoter Support Department of his or her intent to sell the VIVACITY business. No changes in line of sponsorship can result from the sale or transfer of a VIVACITY business.
                    </li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Transfer Upon Death of a Promoter</span> — To effect a testamentary transfer of a VIVACITY business, the successor must provide the following to VIVACITY: (1) an original death certificate; (2) a notarized copy of the will or other instrument establishing the successor’s right to the VIVACITY business; and (3) a completed and executed Promoter Agreement.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Transfer Upon Incapacitation of a Promoter</span> — To effect a transfer of a VIVACITY business because of incapacity, the successor must provide the following to VIVACITY: (1) a notarized copy of an appointment as trustee; (2) a notarized copy of the trust document or other documentation establishing the trustee’s right to administer the VIVACITY business; and (3) a completed Promoter Agreement executed by the trustee.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Succession</span> — Upon the death or incapacitation of a Promoter, his or her business may be passed to his or her heirs. Appropriate legal documentation must be submitted to the Company to ensure the transfer is proper. Accordingly, a Promoter should consult an attorney to assist him or her in the preparation of a will or other testamentary instrument. Whenever a VIVACITY business is transferred by a will or other testamentary process, the beneficiary acquires the right to collect all bonuses and commissions of the deceased Promoter’s marketing organization provided the following qualifications are met. The successor(s) must:
                        <ul style="list-style: inside; padding-left: 50px;">
                            <li style="line-height: 30px;">Execute a Promoter Agreement.</li>
                            <li style="line-height: 30px;">Comply with terms and provisions of the Agreement and meet all of the qualifications for the deceased Promoters’ status.</li>
                            <li style="line-height: 30px;">Provide VIVACITY with an address of record to which all bonus and commission checks will be sent. Bonus and commission checks of a VIVACITY business transferred pursuant to this section will be paid in a single check jointly to the devisees.</li>
                            <li style="line-height: 30px;">Form a business entity and acquire a federal taxpayer Identification number. If the business is bequeathed to joint devisees, VIVACITY will issue all bonus and commission checks and one 1099 to the business entity.</li>
                        </ul>
                    </li>
                </ul>

                <br><br>

                <span style="color: #537518; font-weight: bold;">Your Promoter Status</span><br><br>

                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Active Promoter Status</span> — A Promoter must maintain a minimum monthly Personal Volume (PV) to qualify as an active Promoter. This volume is composed of the Promoter’s personal purchases and personal Retail Customers’ orders (not downline purchases or orders) placed through the Company as described in the VIVACITY Compensation Plan.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Inactive Promoter Status</span> — If a Promoter does not meet the minimum Personal Volume (PV) during a current commission period, the Promoter will be placed on inactive status and will not be eligible to receive overrides or bonuses while inactive.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">“Suspended” Promoter Status</span> — During any suspension period, a Promoter will have all commissions, overrides and bonuses held and will receive no access to the Promoter Only areas of VIVACITY web site. Reinstatement of the Promoter’s Promotership will occur when the basis for the imposition of the suspension is cured.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">“Terminated” Promoter Status</span> — Termination of a Promoter’s Promotership may be done voluntarily by a Promoter, or involuntarily by the Company for violating the Company’s Terms and Conditions of the Promoter Agreement and/or these Policies and Procedures. If a Promoter who has voluntarily terminated his/her Promoter Promotership wishes to return and participate in the program he/she must wait for at least 6 months and then complete the enrollment process as a new Promoter. A Promoter who has terminated, voluntarily or involuntarily, will receive no commissions, overrides and/or bonuses after the termination date.</li>
                </ul>

                <br><br>

                <span style="color: #537518; font-weight: bold;">Sponsoring and Training</span><br><br>

                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Sponsoring and Training Promoters</span> — All Promoter are entitled to sponsor and train other Promoter in the United States of America (and in other countries where the Company is registered and operating) to become part of the Company’s program. Promoters agree to perform a bona fide supervisory, distributive, and selling function in the sales of the Company’s products to the consumer and in training those sponsored. Promoters should endeavor to have continuing contact and communications with, and management supervision of, their marketing organizations. Such supervision may include, but not be limited to, newsletters, written correspondence, personal meetings, telephone contact, voice mail, electronic mail, training sessions, accompanying individuals to Company training, and sharing genealogy information with those sponsored.<br><br>

                        Promoters are also responsible to motivate and train new Promoters in VIVACITY product knowledge, effective sales techniques, the VIVACITY Compensation Plan, and compliance with Company Terms and Conditions of Promoters’ Promotership and these Policies and Procedures. Communication with and the training of Promoters must not, however, violate the policies regarding the development of Promoter -produced sales aids and promotional materials.<br><br>

                        Promoters must monitor the Promoters in their VIVACITY Organizations to ensure that downline Promoters do not make improper product or business claims, or engage in any illegal or inappropriate conduct.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Providing Documentation to Applicants</span> — Promoters must provide the most current version of the Terms and Conditions of the Promoter Agreement, these Policies and Procedures and the Compensation Plan to individuals whom they are sponsoring to become Promoters before the applicant signs a Promoter Agreement. Additional copies of Policies and Procedures can be acquired from VIVACITY. All VIVACITY Agreements and policies are posted on the www.VivacityGO.com website.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Cross-Group Sponsoring</span> — Cross-Group Sponsoring is not allowed and could lead to termination. Cross-Group Sponsoring occurs when a Promoter sponsors an individual who or entity that already has a current Promoter Agreement on file with VIVACITY, or who has had such an agreement within the preceding six calendar months, within a different line of sponsorship. This activity is strictly prohibited. The use of a spouse’s or relative’s name, trade names, DBAs, assumed names, corporations, partnerships, trusts, Federal ID numbers, or fictitious ID numbers to circumvent this policy is prohibited and grounds for immediate termination. Promoters shall not demean, discredit or defame other VIVACITY Promoters in an attempt to entice another Promoter to become part of the first Promoter’s marketing organization. This policy shall not prohibit the transfer of a VIVACITY business in accordance with Section 14. The Company will adjust commissions, bonuses, and volume credits by deducting them from the incorrect Cross-Group Sponsorship.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Sponsorship Change</span> — If a Promoter elects to change sponsors, the change must first be preceded by a written resignation, followed by the Promoter’s remaining inactive for a period of 6 months. The terminated Promoter may re-enroll under the new sponsor. The effect of the resignation of the rights of the Promoter will be the same as a termination, as hereinafter discussed, except that it allows for re-enrollment. Only the Promoter wishing to make the sponsorship change may do so. His/her downline marketing organization will not be moved and will move up to the terminating Promoter sponsor.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Disputes and Multiple Sponsorship</span> — The Company will not mediate disputes involving sponsorship designation. The Company recognizes the sponsor’s number and name that are received during the initial enrollment through the Internet or otherwise. Once the enrollment is completed, and the New Promoter realizes they signed up under the wrong sponsor. They have 10 days to notify VIVACITY in writing, that an error occurred, and a new sponsor will be assigned.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Holding Agreements</span> — Promoters must not manipulate enrollments of new Promoter. All Promoter Agreements must be sent to VIVACITY within 72 hours from the time they are signed by a Promoter, if they are executed in hard copy.</li>
                </ul>

                <br><br>

                <span style="color: #537518; font-weight: bold;">Product Pricing and Purchasing</span><br><br>

                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Purchasing Products</span> — All personal purchases made by Independent Promoters and/or immediate family members of the Promoter, who are living in the same household, must be made through their own WebStore, using their own ID number. Promoters and/or immediate family members, in the same household, are strictly prohibited from ordering through another Promoter’s Website or ID number.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Price List</span> — The Company reserves the exclusive right to change the suggested retail price and Personal Volume (PV) amounts of its products from time to time, and shall give all Independent Promoters at least 30 days prior written notice of any such price change before it becomes effective.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Retail Pricing</span> — Although VIVACITY provides a suggested retail price as a guideline for selling its products, Promoters may sell VIVACITY products at whatever retail price they and their customers agree upon.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Retail Sales</span> — Promoters are compensated based upon retail sales of VIVACITY products. Retail sales provide a foundation for a solid organization. Regardless of their level of achievement, Promoters have an ongoing obligation to continue to personally promote sales through the generation of new customers and through servicing their existing customers.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Purchasing Restrictions</span> — Permissible Promoter purchases shall be automatically modified to comply with the exemption requirements set forth in any state’s laws regulating business opportunities.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Credit/Debit Card Transactions</span> — Promoters choosing to pay for orders by credit/debit card must use their own credit/debit card. If there is a charge-back to the Company by a Promoters caused by the use of an unauthorized credit/debit card or for any other reason, the Promoters will be terminated from the program.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Stop Payments and Credit Card Charge-Backs</span> — A Promoters initiating a credit card charge-back or stop payment without prior written notice to the Company will be subject to termination.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Deposits</span> — No monies should be paid to or accepted by a Promoter for a sale to a personal retail customer except at the time of product delivery. Promoters should not accept monies from retail customers to be held for deposit in anticipation of future deliveries.</li>
                </ul>
                <br><br>

                <span style="color: #537518; font-weight: bold;">Product Sales, Trade Shows, Telephone Sales and Auction</span><br><br>

                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Product Marketing</span> — Promoters may market the Company’s products through direct sales to the end Retail Customer. Because of the nature of the product and legal restrictions, the Company will not permit its product to be sold in retail store-front window displays or on retail stores’ shelves. National retail outlets are not permitted to carry any Company products.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Trade Shows, Expositions and Other Sales Forums</span> — Promoters may display and/or sell VIVACITY products at trade shows and professional expositions. Before submitting a deposit to the event promoter, Promoters must contact Promoter Support in writing for conditional approval, as VIVACITY’s policy is to authorize only one VIVACITY business per event. Final approval will be granted to the first Promoter who submits an official advertisement of the event, a copy of the contract signed by both the Promoter and the event official, and a receipt indicating that a deposit for the booth has been paid. Approval is given only for the event specified. Any requests to participate in future events must again be submitted to Promoter Support. VIVACITY further reserves the right to refuse authorization to participate at any function which it does not deem a suitable forum for the promotion of its products, services, or the VIVACITY opportunity.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Auction Sales and Flea Markets</span> — Promoters shall not sell VIVACITY products via live, silent, Internet, or any other type of auction or flea market due to the nature and image such activities portray.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Telephone Solicitation</span> — The use of any automated telephone solicitation equipment or “boiler room” telemarketing operations in connection with the marketing or promotion of VIVACITY, its products or the opportunity is prohibited.</li>
                </ul>

                <br><br>
                <span style="color: #537518; font-weight: bold;">Replacement and Returns</span><br><br>

                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Product Replacement</span> — In the event that a Promoter or customer receives damaged product, product is missing from a shipment, or incorrect product is included in a shipment, a replacement will be issued at no charge, provided that the Promoter or Customer contacts VIVACITY within 14 days of receipt to report the damage or discrepancy. Arrangements will be made to replace the damaged products ONLY if VIVACITY receives notification within 14 days of receipt. Any request received after the 14-day timeframe will not be honored.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Retail Customers</span> — Retail customers are those customers who purchase directly from the company.</li>
                </ul>

                <br><br>
                <span style="color: #537518; font-weight: bold;">First-Time Retail Customers-30-Day Money-Back Guarantee</span><br><br>
                <div style="line-height: 30px;">Any products that are purchased by a new Retail Customer (Company Direct Customer) may be returned for a full refund of the purchase price, less shipping and handling, provided that the refund is requested within 30 days from the Retail Customer’s first product order with VIVACITY.
                    </div><br><br>
                <span style="color: #537518; font-weight: bold;">Existing Retail Customers</span><br><br>
                <div style="line-height: 30px;">This is a customer who buys VIVACITY products directly from the Company, through the Corporate website, Promoter website, or calls in directly to the call center. If for any reason a Company Direct Customer is dissatisfied with any VIVACITY product, the Customer may return unopened product within 30 days of the original purchase date and receive a refund equal to the purchase price of the product less a 10% restocking fee, including any applicable taxes, less shipping and handling.
                    </div><br><br>
                <span style="color: #537518; font-weight: bold;">Promoter Direct Customer</span><br><br>
                <div style="line-height: 30px;">This is a customer who buys VIVACITY products directly from a Promoter. If for any reason a Promoter Direct Customer is dissatisfied with any VIVACITY product, the First Time Promoter Direct Customer may return the unused portion of the product to the Promoter from whom it was purchased for a replacement or a full refund of the purchase price (less shipping and handling), within 30 days of the original purchase date. An Existing Promoter Direct Customer may return unused and unopened product to the Promoter from whom it was purchased for a replacement or a full refund of the purchase price (less shipping and handling), within 30 days of the original purchase date. Promoters agree to honor the money-back guarantee and will obtain a replacement shipment of identical product from VIVACITY, once the product has been returned to corporate at the Promoter’s expense. The returned package must also contain the name, phone number, and address of the Promoter Direct Customer.
                    </div><br><br>
                <span style="color: #537518; font-weight: bold;">Return Merchandise Procedures</span> — The following refund / replacement procedures apply to all returns for refund or replacement:<br><br>
                <ol style="list-style: inside none inside; padding-left: 20px;">
                    <li style="line-height: 30px;">All products must be returned by either the retail customer or Promoter who originally purchased the product from VIVACITY.</li>
                    <li style="line-height: 30px;">In the case of First-Time Customers, the return must contain the unused portion of the product in its original container. In the case of Existing Customers, only unopened product will be refunded.</li>
                    <li style="line-height: 30px;">Promoter/Customer must request a RMA number from VIVACITY Customer Service, at 800.928.9401, within 30 days of receipt of shipment. Requests received after the 30-day timeframe will not be honored.</li>
                    <li style="line-height: 30px;">Invoice(s) or sales receipts showing purchase of the returned/damaged products from VIVACITY must be enclosed.</li>
                    <li style="line-height: 30px;">All returns must be shipped pre-paid to VIVACITY and must be received at VIVACITY’s distribution center within 14 days of receiving a RMA number. VIVACITY does not accept shipping-collect packages (COD). If returned product is not received by the Company’s distribution center, responsibility for tracing and/or loss of the shipment rests upon the sending party.</li>
                    <li style="line-height: 30px;">Products, sales aids, and kits shall be deemed “resalable” if each of the following elements is satisfied:</li>
                    <li style="line-height: 30px;">The product, sales aid, or kit is unopened and unused.</li>
                    <li style="line-height: 30px;">Packaging and labeling has not been altered or damaged.</li>
                    <li style="line-height: 30px;">The product and packaging are in a condition that would be commercially reasonable within the trade to resell the product at full price.</li>
                    <li style="line-height: 30px;">The product contains current VIVACITY labeling.</li>
                    <li style="line-height: 30px;">Sales aids or kits are currently offered for sale.</li>
                    <li style="line-height: 30px;">The products, sales aids, or kits in question have not been identified or announced as discontinued, non-returnable, one-time only, event-specific, or seasonal.</li>
                </ol>
                <br><br>
                <span style="color: #537518; font-weight: bold;">Ordering and Shipping Policies</span><br><br>
                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">General Order Policies</span> — On mail orders with invalid or incorrect payment, VIVACITY will attempt to contact the Customer by phone, and/or mail to try to obtain another payment. If these attempts are unsuccessful after five working days the order will be returned unprocessed. No C.O.D. orders will be accepted.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Online Ordering Instructions</span> — Promoters wishing to order products through the Internet may do so 24 hours a day, 7 days a week. This service allows Promoters to place orders for products directly over an automated system. The system will calculate item totals, shipping/handling charges and any applicable taxes.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Confirmation of Orders</span> — A Promoter and/or recipient of an order must confirm that the product received matches the product listed on the shipping invoice, and is free of damage. Failure to notify VIVACITY of any shipping discrepancy or damage within seven days of the date that the products were received by the Promoter or Customer will cancel a Promoter’s or Customer’s right to request a correction.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Shipping</span> — All orders will be shipped ground courier, unless otherwise specified. Overnight and 2nd-Day shipping are available for an additional charge per order. Overnight or 2nd-Day shipping does not mean overnight order processing.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Back Orders</span> — VIVACITY will normally ship products within 3 business days from the date on which it receives an order. VIVACITY will expeditiously ship any part of an order currently in stock. If, however, an ordered item is out-of-stock, it will be placed on back order and sent when VIVACITY receives additional inventory. Promoters and/or Customers will not be charged and given Personal Sales Volume until the order is filled. An estimated shipping date will also be provided. Back ordered items may be canceled upon a Customer’s or Promoter’s request.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Shipping and Handling Charges</span> — All orders will have shipping/handling charges applied to them.</li>
                </ul>

                <br><br>

                <span style="color: #537518; font-weight: bold;">Taxes</span><br><br>

                <ul style="list-style: inside none inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Income Taxes</span> — The Company will request and maintain a record of the Promoter’s Social Security Number or Federal Employer Identification Number as provided by the Promoter at the time of enrolling. Every year, the Company will provide, and file with the applicable federal and state agencies, an IRS Form 1099 MISC (Non-employee Compensation) earnings statement to each U.S. resident who falls into one of the following categories:</li>
                    <li style="line-height: 30px;">Had earnings of over $600 in the previous calendar year.<br><br>
                        Each Promoter is responsible for paying local, state, and federal taxes on any income generated as an Independent Promoter. If a VIVACITY business is tax exempt, the Federal tax identification number must be provided to VIVACITY.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Sales Tax Collection</span> — On the sales of the Company’s products, the Company will collect from the Promoter the applicable state sales tax on the wholesale price of the products ordered by the Promoter, as it is for their own personal use. The Company will collect from the Customer the applicable state sales tax on the Preferred or Retail price of the products ordered by the Customer. If a Promoter purchases product at wholesale, and then resells the product at a higher price, the Promoter will be responsible for reporting taxable sales and complying with all rules and regulations as required by the sales tax division of the state where they reside.</li>
                </ul>

                <br><br>

                <span style="color: #537518; font-weight: bold;">The Financial Plan and Compensation</span><br><br>

                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Adherence to the VIVACITY Compensation Plan</span> <span style="color: #ff0000; font-weight: bold;"><a href="https://www.vivacitygo.com/system/themes/prelaunch_lp/images/VivacityCompensationPlan.pdf" target="_blank"> (see full compensation plan here)</a></span> — Promoters must adhere to the terms of the VIVACITY Compensation Plan as set forth in official VIVACITY literature. Promoters shall not offer the VIVACITY opportunity through, or in combination with, any other system, program, or method of marketing other than that specifically set forth in official VIVACITY literature. Promoters shall not require or encourage other current or prospective Customers or Promoters to participate in VIVACITY in any manner that varies from the program as set forth in official VIVACITY literature. Promoters shall not require or encourage other current or prospective Customers or Promoters to execute any agreement or contract other than official VIVACITY agreements and contracts in order to become a VIVACITY Promoter. Similarly, Promoters shall not require or encourage other current or prospective Customers or Promoters to make any purchase from, or payment to, any individual or other entity to participate in the VIVACITY Compensation Plan other than those purchases or payments identified as recommended or required in official VIVACITY literature.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Compensation</span> — Promoters are compensated only for the sale of products, not for sponsoring new Promoters into the program.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Payment Of Commissions, Overrides and Bonuses</span> — Commissions, overrides, and bonuses cannot be paid until both the Promoter and his/her sponsor have completed the Promoter enrollment process.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Errors or Questions</span> — If a Promoter has questions about or believes any errors have been made regarding commissions, bonuses, Activity Reports, or charges, the Promoter must notify VIVACITY in writing within 60 days of the date of the purported error or incident in question. VIVACITY will not be responsible for any errors, omissions or problems not reported to it within 60 days.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Monthly Personal Business Volume</span> — Promoters must meet minimum monthly Personal Volume requirements to qualify for override commissions and bonuses. Failure to meet this qualification will result in a status of “inactive,” and no overrides and/or bonus checks earned for the current commission period will be paid.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Commissions Period</span> — The Company has chosen to pay Promoters monthly on all retail sales, overrides and bonuses. Promoters must review their commission checks and report any discrepancies within 30 days of receipt. All new Promoter enrollments and orders must be received by 6:00 P.M. EST on the last business day of the month. Override and bonus checks will be mailed on or before the 15th of the following month.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Minimum Commission Check</span> — A Promoter must be active and in compliance with the Agreement to qualify for bonuses and commissions. So long as a Promoter complies with the terms of the Agreement, VIVACITY shall pay commissions to such Promoter in accordance with the VIVACITY Compensation plan. No check will be mailed that is less than $25. If a Promoter’s earnings for the month do not meet this requirement, moneys will be carried over to the following month.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Commission Adjustments</span> — Any upline Promoter’s affected by returned products to the Company will accordingly be subject to adjustments in their commissions, overrides and bonus accounts, Personal Volumes, Leadership bonuses, etc. based on all commissions and bonuses paid on the returned products.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Financial Plan Adjustments</span> — The Company reserves the right to modify its Compensation Plan and will notify Promoters of any such changes through its official communications channel(s).</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Unclaimed Commissions and Credits</span> — Promoters must deposit or cash commission and bonus checks within three months from their date of issuance. A check that remains non-cashed after three months will be void.<br>
                        Customers or Promoters who have a credit on account must use their credit within three months from the date on which the credit was issued.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Bonus Buying</span> — Bonus buying includes: (a) the enrollment of an individual or entity as an Promoter without the knowledge of and/or execution of an Promoter Agreement by such individual or entity; (b) the fraudulent enrollment of an individual or entity as an Promoter or Customer; (c) the enrollment or attempted enrolment of non-existent individuals or entities as Promoters or Customers (phantoms); or (d) the use of a credit card by or on behalf of an Promoter or Customer when the Promoter or Customer is not the account holder of such credit card. Bonus buying constitutes a material breach of these Policies and Procedures, and is strictly and absolutely prohibited.</li>
                </ul>

                <br><br>

                <span style="color: #537518; font-weight: bold;">Compliance</span><br><br>

                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Compliance with Government Laws and Regulations</span> — Many cities and counties have laws regulating certain home-based businesses. In most cases these ordinances are not applicable to Promoters because of the nature of their business. However, Promoters must obey those laws that do apply to them. If a city or county official tells a Promoter that an ordinance applies to him or her, the Promoter shall be polite and cooperative, and immediately send a copy of the ordinance to the Compliance Department of VIVACITY. In most cases there are exceptions to the ordinance that may apply to VIVACITY Promoters. Otherwise, Promoters shall comply with all federal, state and local statutes, regulations, ordinances, and applicable tax requirements concerning the operation of their businesses.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Compliance</span> — These Policies and Procedures were created as guidelines for the business relationship and contractual covenants and obligations between the Company and all Independent Promoters. They help ensure the proper operation of the marketing plan on a day-to-day basis. Violation of the Terms and Conditions of the Promoter Agreement, these Policies and Procedures, or any illegal, fraudulent, deceptive, improper, threatening or unethical business conduct by a Promoter may result, at VIVACITY’s discretion, in one or more of the following corrective measures:</li>
                    <li style="line-height: 30px;">Issuance of a written warning or admonition</li>
                    <li style="line-height: 30px;">Requiring the Promoter to take immediate corrective measures</li>
                    <li style="line-height: 30px;">Imposition of a fine, which may be withheld from bonus and commission checks</li>
                    <li style="line-height: 30px;">Loss of rights to one or more bonus and commission checks</li>
                    <li style="line-height: 30px;">Withholding from an Promoter all or part of the Promoter’s bonuses and commissions during the period that VIVACITY is investigating any conduct allegedly violative of the Agreement. If an Promoter’s business is canceled for disciplinary reasons, the Promoter will not be entitled to recover any commissions withheld during the investigation period</li>
                    <li style="line-height: 30px;"> Suspension of the individuals’ Promoter Agreement for one or more pay periods</li>
                    <li style="line-height: 30px;">Termination of the offenders’ Promoter Agreement</li>
                    <li style="line-height: 30px;">Following any other measure expressly allowed within any provision of the Agreement or which VIVACITY deems practicable to implement and appropriate to equitably resolve injuries caused partially or exclusively by the Promoter’s policy violation or contractual breach</li>
                    <li style="line-height: 30px;">Institute legal proceedings for monetary and/or equitable relief.</li>
                </ul>

                <br><br>

                <span style="color: #537518; font-weight: bold;">Advertising and the Media</span><br><br>


                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Sales Materials and Literature</span> — Independent Promoters may purchase sales materials, brochures, and literature approved by and available from the Company or its authorized Marketing Support Centers. Only Company-produced materials are permitted, and the Promoter agrees to use only Company-approved sales materials and literature.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Advertising</span> — The Company will supply guidelines for advertising. All forms of media advertising must be approved by the Company. The use of the Company’s trade name, logos, trademarks, product names and/or copyrighted materials is not permitted without prior written Company approval. Use of any of the above without permission will result in termination of the Promoter’s Promotership.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Media Contacts and Inquiries</span> — Promoters are prohibited from: (a) initiating contact with the media (radio, television, newspaper, tabloid, magazine or any other media outlet); or (b) making, engaging in, or participating in any appearance, interview, or any other type of statement to the media, if such contact, appearance, interview, of statement in any way involve the Company, its products, its Promoters, or the subject Promoter’s VIVACITY business without the prior written approval of the Company. Any media inquiries must be immediately referred to the Company.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Media Advertising Inquiries</span> — Promoters must obtain prior written approval of the Company before the publication or airing of any advertising, in any form, involving the Company, its products, or their individual VIVACITY business.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Forms</span> — Promoter must use the authorized forms provided by the Company. Agreements and orders will not be accepted or processed on any other forms or worksheets.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Imprinted Checks</span> — Promoters are not permitted to use the Company’s trade name or any of its trademarks on their business or personal checking accounts or checks.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Business Cards, Stationery, and Printed Materials</span> — Independent Promoters may purchase business cards, stationery, and printed materials approved by and available from the Company or its authorized Marketing Support Centers. Only Company-produced materials are permitted, and the Promoter agrees to use only Company-approved business cards, stationery, and printed materials.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Trademark Guidelines</span> — The names VIVACITY, Awaken-S7, RE-VIVE, Daily Detox, LifeShield, and Ignite EFC, as well as the Company’s logo, literature and product names are protected by trademark and copyright laws, and may not be used for any business purpose without the company’s prior written authorization. Independent Promoters are prohibited from producing, duplicating, altering or procuring from outside sources any literature, sales aids, or sales promotional material using the Company’s name, logos or trademarks without prior written permission from the Company, except as follows:

                        <ul style="list-style: inside; padding-left: 40px;">
                            <li style="line-height: 30px;">Promoter’s Name</li>
                            <li style="line-height: 30px;">Vivacity Promoter</li>
                        </ul>
                        <br><br>
                        Any violation of this policy may result in the Promoter’s termination.
                    </li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Yellow and White Page Telephone Listings</span> — Promoters may list themselves as an Independent VIVACITY Promoter in the white and/or yellow pages of the telephone directory under their own name. In the event more than one Promoter wishes to place a listing in the same directory, the telephone company must be advised that such listing is to be in alphabetical order by the Promoter’s last name under the heading: Vivacity Promoter</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Toll-Free, “800”, Telephone Number Listing</span> — Promoters are strictly prohibited from listing their toll-free, “800”, numbers under the Company’s trade and/or product names in a manner that could indicate to a third party that the listing is for and by the Company rather than the Promoter as an independent contractor.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Telephone Answering</span> — Promoters may not answer the telephone by saying, Vivacity or in any other manner that would lead callers to believe that they have reached the Corporate offices of the Company.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Promoter Produced Promotional Materials</span> — There can be no information contrary to that contained in the Company sales promotional materials or literature which becomes incorporated into any form of public advertising and information distributed by the Independent Promoter. Further, only the pre-approved ad slicks may be used by a Promoter in advertising. Any additional or different ad requires prior written Company approval. Failure to do so will result in termination and possible legal action.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Endorsements</span> — No endorsements by Company officers, administrators or outside third parties may be alleged, except as specifically communicated and approved of in Company literature and communications.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Recordings</span> — Promoters shall not produce or reproduce for sale any personal or Company-produced audio- or video-taped material detailing the Company’s opportunity or product presentations, events or speeches, including conference calls. Video and/or audio taping of Company meetings and conferences is strictly prohibited. Still photography is allowable at the discretion of the meeting host.</li>
                </ul>


                <br><br>

                <span style="color: #537518; font-weight: bold;">Internet Policies</span><br><br>

                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Advertising Policies</span> — All of the previous advertising policies also refer to any Internet solicitation of VIVACITY products and/or the business, including emails, websites, mailing lists and any other form of Internet presence.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">“Anti-Spamming” and Unsolicited Faxes Policy</span> — VIVACITY, does not accept “spamming” as an acceptable form of mass marketing of the VIVACITY business opportunity or the VIVACITY product line. The practice of sending mass, unsolicited e-mail is harmful to both the Promoter and Company, creating bad will between the Internet community and our company.<br><br>
                        Promoters may not use or transmit unsolicited faxes, mass e-mail distribution, unsolicited e-mail, or “spamming” relative to the operation of their VIVACITY business.<br><br>

                        The terms “unsolicited faxes” and “unsolicited e-mail” mean the transmission via telephone facsimile or electronic mail, respectively, of any material or information advertising or promoting VIVACITY, its products, its compensation plan or any other aspect of the company which is transmitted to any person. These terms do not include a fax or e-mail: (a) to any person with that person’s prior express invitation or permission; or (b) to any person with whom the Promoter has an established business or personal relationship. The term “established business or personal relationship” means a prior or existing relationship formed by a voluntary two-way communication between a Promoter and a person, on the basis of: (a) an inquiry, application, purchase or transaction by the person regarding products offered by such Promoter; or (b) a personal or familial relationship, which relationship has not been previously terminated by either party.<br><br>

                        Violation of this policy may result in sanctions against your Promoter Promotership including suspension or termination of your Promoter Promotership account.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Web Pages</span> — If a Promoter desires to utilize an Internet web page to promote his or her business, he or she may do so through the company’s official Promoter web sites. Under no circumstances is a Promoter allowed to use the Company’s trade name, logos, trademarks, product names and/or copyrighted materials on any other website, or on-line publications.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Domain Names</span> — Promoters may not use or attempt to register any of VIVACITY’s trade names, trademarks, service names, service marks, product names, the Company’s name, or any derivative thereof, for any Internet domain name.</li>
                </ul>

                <br><br>

                <span style="color: #537518; font-weight: bold;">Claims</span><br><br>

                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Income Claims</span> — In their enthusiasm to enroll prospective Promoters, some Promoters are occasionally tempted to make income claims or earnings representations to demonstrate the inherent power of network marketing. This is counterproductive because new Promoters may become disappointed very quickly if their results are not as extensive or as rapid as the results others have achieved. At VIVACITY, we firmly believe that the VIVACITY income potential is great enough to be highly attractive, without reporting the earnings of others.
                        <br><br>
                        Moreover, the Federal Trade Commission and several states have laws or regulations that regulate or even prohibit certain types of income claims and testimonials made by persons engaged in network marketing. While Promoters may believe it beneficial to provide copies of checks, or to disclose the earnings of themselves or others, such approaches have legal consequences that can negatively impact VIVACITY as well as the Promoter making the claim unless appropriate disclosures required by law are also made contemporaneously with the income claim or earnings representation. Because VIVACITY Promoters do not have the data necessary to comply with the legal requirements for making income claims, a Promoter, when presenting or discussing the VIVACITY opportunity or the VIVACITY Compensation Plan to a prospective Promoter, may not make income projections, income claims, or disclose his or her VIVACITY income (including the showing of checks, copies of checks, bank statements, or tax records). Hypothetical income examples that are used to explain the operation of the VIVACITY Compensation Plan, and which are based solely on mathematical projections, may be made to prospective Promoters, so long as the Promoter who uses such hypothetical examples makes clear to the prospective Promoter (s) that such earnings are hypothetical.</li>
                    <li style="line-height: 30px;">Furthermore, any profits or success resulting from activities as an Promoter will be based only on sales volume of products offered by the Company and the Promoter or his/her personal groups or organization. Any success achieved will be based completely upon the independent Promoter’s own efforts, commitment, and skill.</li>
                    <li style="line-height: 30px;">A Promoter understands and shall make it clear to any Promoters he/she may sponsor, that Promoters will not be successful merely in sponsoring others without substantial efforts being directed at retail sales. The Company believes firmly that the income potential is great enough to be highly attractive in reality without resorting to artificial and unrealistic projections.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Product Medical Claims</span> — Promoter understand and agree to represent that all Company products are sold as consumer items and not medical products. Promoter may not use words such as therapy, therapeutic, cures, heals, healing, speeds or promotes healing, claims of cures, healing or any other medical claims for specific ailments, reference to research and/or clinical studies, or any other statements not used in the Company’s literature.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Medical Treatment, Approval and Therapy</span> — Promoters understand that they will NOT say or imply that the products are FDA-approved, or discuss or suggest any diagnosis, evaluation, prognosis, description, treatment, therapy, or management or remedy of illness, ailment or disease. Promoters understand that the Company’s products are NOT offered or intended or considered as medicines or medical treatment for any disorder or disease, either mental or physical.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Governmental Endorsement</span> — Federal and state regulatory agencies do not approve or endorse network programs. Therefore, Promoters may not represent or imply, directly or indirectly, that the Company’s program has been approved or endorsed by any governmental agency.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Product Claims</span> — The Company assumes no responsibility and/or liability for any oral claims made by its Promoters, customers, employees or advocates. The Company assumes no responsibility and/or liability for any written claims made by its Promoters or customers.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Consistent and Accurate Statement</span> — Promoters must present statements about the Company’s products that are in accordance with the sales and training materials provided by the Company in the training materials, brochures, newsletters, etc. The Company has provided specific guidelines for presenting clear, consistent and accurate statements about the products. Promoters agree to follow these guidelines.</li>
                </ul>

                <br><br>

                <span style="color: #537518; font-weight: bold;">Disputes and Policy Violations</span><br><br>
                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Grievances and Complaints</span> — When a Promoters has a grievance or complaint with another Promoters regarding any practice or conduct in relationship to their respective VIVACITY businesses, the complaining Promoters should first report the problem to his or her Sponsor who should review the matter and try to resolve it with the other party’s upline sponsor. If the matter cannot be resolved, it must be reported in writing to the Promoter Support Department at the Company. The Promoter Support Department will review the facts and attempt to resolve it.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Reporting Policy Violations</span> — Promoters observing a Policy violation by another Promoter should submit a written report of the violation directly to the attention of the VIVACITY Compliance Department. Details of the incidents such as dates, number of occurrences, persons involved, and any supporting documentation should be included in the report.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Arbitration</span> — Any controversy or claim arising out of or relating to the Agreement, or the breach thereof, shall be settled by arbitration administered by the American Arbitration Association under its Commercial Arbitration Rules, and judgment on the award rendered by the arbitrator may be entered in any court having jurisdiction thereof. If an Promoter files a claim or counterclaim against VIVACITY, he or she may only do so on an individual basis and not with any other Promoters or as part of a class or consolidated action. Promoters waive all rights to trial by jury or to any court. All arbitration proceedings shall be held in the City of Florence, Alabama, unless the laws of the state in which an Promoter resides expressly require the application of its laws, in which case the arbitration shall be held in the capital of that state. The parties shall be entitled to all discovery rights allowed under the Federal Rules of Civil Procedure. No other aspects of the Federal Rules of Civil Procedure shall be applicable to arbitration. There shall be one arbitrator, an attorney at law, who shall have expertise in business law transactions with a strong preference being an attorney knowledgeable in the direct selling industry, selected from the panel which the American Arbitration Panel provides. The prevailing party shall be entitled to receive from the losing party costs and expenses of arbitration, including legal and filing fees. The decision of the arbitrator shall be final and binding on the parties and may, if necessary, be reduced to a judgment in any court of competent jurisdiction. This agreement to arbitrate shall survive any termination or expiration of the Agreement.</li>
                    <li style="line-height: 30px;"> Nothing in these Policies and Procedures shall prevent VIVACITY from applying to and obtaining from any court having jurisdiction a writ of attachment, a temporary injunction, preliminary injunction, permanent injunction or other relief available to safeguard and protect VIVACITY’s interest prior to, during or following the filing of any arbitration or other proceeding or pending the rendition of a decision or award in connection with any arbitration or other proceeding.</li>
                </ul>

                <br><br>

                <span style="color: #537518; font-weight: bold;">Termination</span> <br><br>


                <ul style="list-style: inside; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Effect of Termination</span> — So long as a Promoter remains active and complies with the Terms and Conditions of the Promoter Agreement and these Policies and Procedures, VIVACITY shall pay commissions to such Promoter in accordance with the Compensation Plan. A Promoter’s bonuses and commissions constitute the entire consideration for the Promoter’s efforts in generating sales and all activities related to generating sales (including building a downline marketing organization). Following a Promoter’s non-renewal of his or her Promoter Agreement, termination for inactivity, or voluntary or involuntary termination of his or her Promoter Agreement (all of these methods are collectively referred to as termination), the former Promoter shall have no right, title, claim or interest to the marketing organization which he or she operated, or any commission or bonus from the sales generated by the organization. A Promoter whose business is terminated will permanently lose all rights as an Promoter. This includes the right to sell VIVACITY products and services and the right to receive future commissions, bonuses, or other income resulting from the sales and other activities of the Promoter’s former downline marketing organization. In the event of termination, Promoters agree to waive all rights they may have, including, but not limited to, property rights, to their former retail customers, to their former downline marketing organization, as well as prospective downline marketing organization members and retail customers, and to any bonuses, commissions, or other remuneration derived from the sales and other activities from his or her former downline marketing organization and former retail customers.</li>
                    <li style="line-height: 30px;">The former Promoter shall not hold himself or herself out as a VIVACITY Promoter and shall not have the right to sell VIVACITY products or services. A Promoter whose Promoter Agreement is terminated shall receive commissions and bonuses only for the last full pay period he or she was active prior to termination (less any amounts withheld during an investigation preceding an involuntary termination).</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Termination Due to Inactivity</span> — It is the Promoter’s responsibility to lead his or her marketing organization with the proper example in personal production of sales to end consumers by complying with the sales volume requirements of the Compensation Plan. Without satisfying this requirement, the Promoter will lose his or her right to receive commissions from sales generated through his or her marketing organization. Therefore, Promoters who personally produce less than the required amount of Personal Volume (PV) as specified in the Compensation Plan for any pay period will not receive a commission for the sales generated through their marketing organization for that pay period. If a Promoter has not produced any personal sales, and has not maintained a Promoter website for a period of six consecutive calendar months, his or her Promoter Agreement shall be terminated for inactivity. The termination will become effective on the day following the last day of the 6th month of inactivity. VIVACITY will not provide written confirmation of termination.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Involuntary Termination</span> — A Promoter’s violation of any of the Terms and Conditions of the Promoter Agreement or these Policies and Procedures, including any amendments that may be made by VIVACITY in its sole discretion, including the involuntary termination of his or her Promoter Agreement. Termination shall be effective on the date on which written notice is mailed, return receipt requested, to the Promoter’s last known address, or when the Promoter receives actual notice of termination, whichever occurs first.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Voluntary Termination</span> — A participant in this network marketing plan has a right to cancel at any time, regardless of reason. Cancellation must be submitted in writing to the company at its principal business address. The written notice must include the Promoter’s signature, printed name, address, and Promoter I.D. Number.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Non-renewal</span> — A Promoter may also voluntarily terminate his or her Promoter Agreement by failing to renew the Agreement on its anniversary date.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Compression (Roll-Up) of Promoters</span> — When a vacancy occurs in an Organization due to the termination of a VIVACITY business, each Promoter below the terminated Promoter will roll up to sponsor of the terminated Promoter.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Promoter Returns on Starter Packs</span> — A new Promoter has 30 days from the order date of his / her Starter Kit in which to cancel the Promoter Application and receive a full refund of their enrollment fee and / or Starter Pack fee. The Promoter will receive a refund, less shipping and handling, upon VIVACITY receiving the Starter Pack with original products and materials in unopened, unused, and resalable condition as originally sent. VIVACITY will not issue a refund for a Starter Pack containing opened product. If a partial kit is returned, the wholesale price of the product less 10% restocking fee will be refunded. After 30 days, VIVACITY deems any new Promoter who has not returned the Starter Pack to have irrevocably accepted the Terms and Conditions of Promoter and any / all policies and procedures of VIVACITY, and to be bound thereby. No refunds of Starter Packs will be allowed after the 30-day period has expired.</li>
                    <li style="line-height: 30px;">Any refunds issued shall result in an offset to Promoters who receive commissions, overrides, and bonuses paid out by VIVACITY in conjunction with the original purchase.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Return of Product by Promoters</span> - A Promoter who returns product for a refund is deemed to voluntarily cancel their Promotership. A Promoter may replace damaged product, without jeopardizing their Promotership.</li>
                </ul>

                <br><br>

                <span style="color: #537518; font-weight: bold;">General Provisions</span><br><br>



                <ul style="list-style: inside none none; padding-left: 20px;">
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Non-disparagement</span> — VIVACITY wants to provide its Independent Promoters with the best products, compensation plan, and service in the industry. Accordingly, we value your constructive criticisms and comments. All such comments should be submitted in writing to the Promoter Support Department. While VIVACITY welcomes constructive input, negative comments and remarks made in the field by Promoters about the Company, its products, or financial plan serve no purpose other than to sour the enthusiasm of other VIVACITY Promoters. For this reason, and to set the proper example for their downline, Promoters must not disparage VIVACITY, other VIVACITY Promoters, VIVACITY’s products, the Compensation plan, or VIVACITY’s directors, officers, or employees. The disparagement of VIVACITY, other VIVACITY Promoters, VIVACITY’s products, the Compensation plan, or VIVACITY’s directors, officers, or employees constitutes a material breach of these Policies and Procedures. Any such disparaging activity will be deemed grounds for termination of the individual’s Promotership.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Updated Literature and Information</span> — Promoters are responsible for learning updated information pertaining to the Company and for disseminating that information to their organizations. New forms and literature may periodically replace old forms and literature. Once new forms and literature are available, old materials will cease to be effective and valid. Refer to the revision code at the bottom of each form and piece of literature to determine the most recent version.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Company Vendor Contact</span> — Promoters are not permitted to have direct or indirect contact with the Company’s trade vendors for the purposes of attempting to negotiate “special” terms for products, promotional items, sales literature, etc., or to interfere with the Company’s relationship with its vendors. Any such contact will be deemed grounds for termination of the individual’s Promotership.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Re-packaging and Re-labeling Prohibited</span> — Promoters may not re-package, re-label, refill or alter the labels on any VIVACITY products, information, materials or programs in any way. VIVACITY products must be sold in their original containers only. Such re-labeling or repackaging would likely violate federal and state laws, which could result in severe criminal penalties. You should also be aware that civil liability can arise when, as a consequence of the re-packaging or re-labeling of products, the persons using the products suffer any type of injury or their property is damaged.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Non-Competition and Non-Solicitation</span> — VIVACITY Promoters are free to participate in other, Promoter programs, multilevel or network marketing business ventures or marketing opportunities (collectively network marketing). However, during the term of this Agreement, and for a period of three months following the termination of this Agreement, Promoters may not recruit other VIVACITY Promoters or Customers for any other Promoter programs, and/or another network marketing business. The term recruit means actual or attempted solicitation, enrollment, encouragement, or effort to influence in any other way, either directly or through a third party, another VIVACITY Promoter or customer to enroll or participate in another, Promoter program, multilevel marketing, network marketing or direct sales opportunity. This conduct constitutes recruiting even if the solicited Promoter does not join, or the solicited customer does not make a purchase. This conduct also constitutes recruiting if the Promoter’s actions are in response to an inquiry made by another Promoter or Customer. Promoters, who qualify for Sr. Executive compensation, will not be eligible to receive Sr. Executive compensation if they are participating in other Promoter programs, multilevel or network marketing business ventures or marketing opportunities (collectively network marketing).</li>
                    <li style="line-height: 30px;">During the term of this Agreement and for a period of one year following the termination of this Agreement, Promoters shall not approach, solicit, induce or entice any VIVACITY Promoter, customer, supplier, or employee to alter, in any way, his or her business or employment relationship with VIVACITY.</li>
                    <li style="line-height: 30px;">Promoters must not sell, or attempt to sell, any competing non-VIVACITY products or services to VIVACITY Promoters or Customers. Any product or services in the same generic category as a VIVACITY product or service is deemed to be competing (e.g., any Skin Care product is in the same generic category as VIVACITY’s Skin Care products, and is therefore a competing product, regardless of differences in cost, quality, or ingredients.)</li>
                    <li style="line-height: 30px;">Promoters may not display VIVACITY products or services with any other products or services in a fashion that might in any way confuse or mislead a prospective customer or Promoter into believing there is a relationship between the VIVACITY and non-VIVACITY products or services. Promoters may not offer the VIVACITY opportunity, products or services to prospective or existing Customers or Promoters in conjunction with any non-VIVACITY program, opportunity, product or service. Promoters may not offer any non-VIVACITY opportunity, products or services at any VIVACITY-related meeting, seminar or convention, or immediately following such event.</li>
                    <li style="line-height: 30px;">Promoter acknowledges and agrees that the foregoing provisions regarding Non-Competition and Non-Solicitation are necessary for VIVACITY to preserve and protect its valuable interests and agrees that such provisions shall survive the termination of the Agreement.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">No Circumvention</span> — The Company, at its sole discretion, hereby reserves the right to take action or to refuse to take action such as may be necessary to ensure compliance with its Policies or applicable law. Specifically, the Company may refuse to honor certain Promoter requests or to take other preventative action in situations whereby the Company deems a Promoter is acting to circumvent compliance with the Policies or applicable law. The preceding is not the exclusive remedy, but is cumulative with all other remedies, which may be available to the Company at law or in equity.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Other Agreements</span> — The Promoter acknowledges and agrees that entering into this Agreement does not violate or breach any other agreement the Promoter may have with any other person or entity.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Indemnification</span> — A Promoter is fully responsible for all of his or her verbal and written statements made regarding VIVACITY products, services, and the VIVACITY Compensation Plan, which are not expressly contained in official VIVACITY materials. Promoters agree to indemnify VIVACITY and VIVACITY directors, officers, employees, and agents, and hold them harmless from any and all liability including judgments, civil penalties, refunds, attorney fees, court costs, or lost business incurred by VIVACITY as a result of the Promoter’s unauthorized representations or actions. This provision shall survive the termination of the Promoter Agreement.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Amendments</span> — Because federal, state and local laws, as well as the business environment, periodically change, VIVACITY reserves the right to amend the Agreement and its prices in its sole and absolute discretion. By signing the Promoter Agreement, a Promoter agrees to abide by all amendments or modifications that VIVACITY elects to make. Amendments shall be effective upon notice to all Promoters that the Agreement has been modified. Notification of amendments shall be published in official VIVACITY materials. The Company shall provide or make available to all Promoters a complete copy of the amended provisions by one or more of the following methods: (1) posting on the Company’s official web site; (2) electronic mail (e-mail). The continuation of a Promoter’s VIVACITY business or an Promoter’s acceptance of bonuses or commissions constitutes acceptance of any and all amendments.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Delays</span> — VIVACITY shall not be responsible for delays or failures in performance of its obligations when performance is made commercially impracticable due to circumstances beyond its reasonable control. This includes, without limitation, strikes, labor difficulties, riot, war, fire, death, curtailment of a party’s source of supply, or government decrees or orders.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Governing Law, Jurisdiction and Venue</span> — Jurisdiction and Venue of any matter not subject to arbitration shall reside in Florence, Alabama unless the laws of the state in which a Promoter resides expressly require the application of its laws. The Federal Arbitration Act shall govern all matters relating to arbitration. The law of the State of Alabama shall govern all other matters relating to or arising from the Agreement unless the laws of the state in which a Promoter resides expressly require the application of its laws.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Waiver</span> — The Company never gives up its right to insist on compliance with the Agreement and with the applicable laws governing the conduct of a business. No failure of VIVACITY to exercise any right or power under the Agreement or to insist upon strict compliance by a Promoter with any obligation or provision of the Agreement, and no custom or practice of the parties at variance with the terms of the Agreement, shall constitute a waiver of VIVACITY’s right to demand exact compliance with the Agreement. Waiver by VIVACITY can be affected only in writing by an authorized officer of the Company. VIVACITY’s waiver of any particular breach by a Promoter shall not affect or impair Promoter’s rights with respect to any subsequent breach, nor shall it affect in any way the rights or obligations of any other Promoter. Nor shall any delay or omission by VIVACITY to exercise any right arising from a breach affect or impair VIVACITY’s rights as to that or any subsequent breach.<br><br>

                        The existence of any claim or cause of action of a Promoter against VIVACITY shall not constitute a defense to VIVACITY’s enforcement of any term or provision of the Agreement.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Indemnification</span> — The Promoter agrees to hold the Company harmless from, and to indemnify it as regards, any loss, cause of action, litigation, claim, debt, judgment, attachment, execution, demand, cost (including but not limited to attorneys’ fees) or other obligation of any kind arising out of the Independent Promoter’s acts, words, conduct or omission as an independent contractor for the sale of Company products.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Notice</span> — Any communication, notice or demand of any kind whatsoever which either the Promoter or the Company may be required or may desire to give or to serve upon the other shall be in writing and delivered by telex, telegram, e-mail or facsimile (if confirmed in writing sent by registered or certified mail, postage prepaid, return receipt requested or by personal service), or by registered or certified mail, postage prepaid, return receipt requested, or by personal service. Any party may change its address for notice by giving written notice to the other in the manner provided in these Policies and Procedures. Any such communication, notice or demand shall be deemed to have been given or served on the date personally served by personal service, on the date of confirmed dispatch if by electronic communication, or on the date shown on the return receipt or other evidence if delivered by mail.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Severability</span> — Should any portion of these Policies and Procedures or the Promoter Terms and Conditions, or any other instrument or document referred to herein or issued by the Company, be declared invalid by a court of competent jurisdiction, the balance of all of such shall remain in full force and effect.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Limitation of Damages</span> — TO THE EXTENT PERMITTED BY LAW, THE COMPANY AND ITS PROMOTERS, OFFICERS, DIRECTORS, EMPLOYEES, AND OTHER REPRESENTATIVES SHALL NOT BE LIABLE FOR, AND PROMOTER HEREBY RELEASES THE FORGOING FROM AND WAIVES, ANY CLAIM FOR LOSS OF PROFIT, INCIDENTAL, SPECIAL, CONSEQUENTIAL OR EXEMPLARY DAMAGES WHICH MAY ARISE OUT OF ANY CLAIM WHATSOEVER RELATING TO THE COMPANY’S PERFORMANCE, NON-PERFORMANCE, ACT OR OMISSION WITH RESPECT TO THE BUSINESS RELATIONSHIP OR OTHER MATTERS BETWEEN ANY PROMOTER AND THE COMPANY, WHETHER SOUNDING IN CONTRACT, TORT OR STRICT LIABILITY. Furthermore, it is agreed that any damages to Promoter shall not exceed, and is hereby expressly limited to, the amount of the unsold products and/or services owned by the Promoter and any commissions, overrides and bonuses owed to the Promoter.</li>
                    <li style="line-height: 30px;"><span style="color: #537518; font-weight: bold;">Entire Agreement</span> — This statement of Policies and Procedures which is incorporated into the Associate Agreement (Terms and Conditions), along with the Company’s Compensation Plan, constitutes the entire agreement of the parties regarding their business relationship.
                        Some states do not allow the limitation of liability, so this provision may not apply to you.</li>
                </ul>


                <br><br>

                <span style="color: #537518; font-weight: bold; font-size: 20px;">TERMS OF PROMOTER AGREEMENT</span><br><br>
                <div style="line-height: 30px;">1.  I certify that I am of legal age for the state in which I reside and that the Social Security (or Federal Tax ID) Number listed on the Affiliate Application is my correct taxpayer identification number.<br><br>
                2.  I have not been a Vivacity Promoter, or a partner, shareholder, or principal in any entity having a Vivacity Promoter within the past six months.<br><br>
                3.  Upon acceptance of this Agreement by Vivacity, I will become a Vivacity Promoter. I understand that as a Vivacity Promoter:<br><br>
                I may purchase products and services from Vivacity at the Affiliate Price.<br><br>
                I have the right to offer for sale Vivacity products and services in accordance with these Terms and Conditions and the Affiliate Policies and Procedures.<br><br>
                I have the right to enroll persons in Vivacity.<br><br>
                I will assist, train, and motivate the Affiliates in my downline marketing organization on a “best efforts” basis.<br><br>
                I shall comply with all federal, state, county and municipal laws, ordinances, rules, and regulations, and shall make all reports and remit all withholdings or other deductions as may be required by any federal, state, county or municipal law, ordinance, rule or regulation.<br><br>
                I will develop, service, and/or maintain the number of retail sales and/or customers per month specified in the compensation plan for my level of commission. I understand that I must keep accurate records of retail sales and Vivacity may periodically ask me to provide documentation of such sales to Vivacity.<br><br>
                In order to be eligible to receive bonuses and commissions, at least 70% of my Personal Volume should be sold to customers. I will not purchase any products or services solely for the purpose of qualifying for overrides, commissions or bonuses.<br><br>
                I will only use the sales contracts and order forms which are provided by Vivacity for the sales of its goods and services, and I will follow all policies and procedures established by Vivacity for the completion and processing of such contracts and orders.<br><br>
                4.  I agree to present the Vivacity Compensation Plan and Vivacity products and services as set forth in official Vivacity literature. I will make no claims regarding potential income, earnings, products or services beyond what is stated in official Vivacity literature. I will not: (a) use, produce, create, publish, distribute, or obtain from any source other than Vivacity, any literature, recordings (audio, video, or otherwise), sales or enrollment aids relating to Vivacity products, services or the Vivacity Compensation Plan; (b) use or display, either in print or on the internet, any Vivacity trademarks, trade names, service marks, logos, designs or symbols; (c) advertise Vivacity products, services or the Vivacity business opportunity.<br><br>
                5.  I agree that as a Vivacity Affiliate I am an independent contractor, and not an employee, agent, partner, legal representative, or franchisee of Vivacity I am not authorized to and will not incur any debt, expense, obligation, or open any checking account on behalf of, for, or in the name of Vivacity I understand that I shall control the manner and means by which I operate my Vivacity Promotership, subject to my compliance with these Terms and Conditions, the Vivacity Policies and Procedures and the Vivacity Compensation Plan (all of which are collectively referred to as the “Agreement”). I agree that I will be solely responsible for paying all expenses incurred by myself. I UNDERSTAND THAT I SHALL NOT BE TREATED AS AN EMPLOYEE OF VIVACITY FOR FEDERAL OR STATE TAX PURPOSES, OR FOR STATE OR FEDERAL EMPLOYMENT PURPOSES. Vivacity is not responsible for withholding, and shall not withhold or deduct from my bonuses and commissions, if any, FICA, or taxes of any kind, unless such withholding becomes legally required. I agree to be bound by all sales tax collection agreements between Vivacity. and all appropriate taxing jurisdictions, and all related rules and procedures.<br><br>
                6.  I have carefully read and agree to comply with the Vivacity Policies and Procedures and the Vivacity Compensation Plan. I understand that I must be in good standing, and not in violation of any of the terms of this Agreement, in order to be eligible to receive any bonuses or commissions from Vivacity I understand that these Terms and Conditions, the Vivacity Policies and Procedures, or the Vivacity Financial Plan may be amended from time to time, without notice, and any such amendment shall be binding to this Agreement. Notification of amendments shall be published at the Vivacity Corporate Website. The continuation of my Vivacity Promotership or my acceptance of bonuses or commissions shall constitute my acceptance of any and all amendments.<br><br>
                7.  The term of this Agreement is one year. If I fail to annually renew my Vivacity business and pay the required annual renewal fee, or if it is canceled or terminated for any reason, I understand that I will permanently lose all rights as a Promoter. I shall not be eligible to sell Vivacity products and services nor shall I be eligible to receive commissions, bonuses, or other income resulting from the activities of my former downline marketing organization, and former retail customers. In the event of cancellation, termination or non-renewal, I agree to waive all rights I have, including but not limited to property rights, to my former downline marketing organization, and retail customers, as well as prospective downline marketing organization members and retail customers, and to any bonuses, commissions or other remuneration derived through the sales and other activities of my former downline marketing organization, and retail customers.
                    </div>

            </div>

        </div>



          <!--  <input type="checkbox" name="iagreetoit" id="iagreetoit" value="Y"/>
            I agree to your Terms &amp; Conditions.
           -->



        <div class="ckbox2" style="width:100%;">
            <input  type="checkbox" name="iagreetoit" id="iagreetoit" value="Y"/>
            <span style="margin-top: -2px">Click Here To Accept The Promoter Agreement</span>
        </div>

        <div>
            <input type="submit" value="Submit">
        </div>

    </form>
    </div>
    <?php
}




function getmailbody($username = '')
{
    return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Enrollment</title>
</head>

<body>

<table width="100%" border="0">
  <tr>
    <td align="center">
        <table width="600" border="0" style="font-family:Arial, Helvetica, sans-serif;">
  <tr>
    <td align="left" valign="middle" style="padding:15px; padding-bottom:0px;"><img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/logo-enrollment.png"  alt="#" style="width:230px;"/></td>
    
    <td align="right" valign="middle" style="padding:15px; padding-bottom:0px; font-family:Arial, Helvetica, sans-serif; font-size:15px; color:#3c3c3b; line-height:20px; font-weight:bold;">Financial Freedom.<br />

Premium Quality Products.<br />
Generous Comp Plan.<br />
Be Your Own Boss Now!</td>
  </tr>
  
  <tr>
    <td colspan="2" align="center" valign="middle"><img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/mailpagebanner1.jpg"  alt="#"/>
    
    </td>
    </tr>
    
    <tr>
    <td colspan="2" align="center" valign="middle" style="padding:15px 40px; font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:15px; color:#3c3c3b; line-height:20px;">
    
    
    We understand our sucess is deeply rooted and intertwined with our most important businesspartner - YOU! <br />
<br />


Lets live a life full of vitality, inspiration, and health. By join Vivacity, you’ve taking a steptowards <span style="text-transform:uppercase; color:#13a716;">the shift</span> towards permanent 
transformation in mind, body, and soul.  <br />
<br />


Congrats on taking the first step towards experiencing the vital essence of an inspired life.  
    
    </td>
    </tr>
    
    
    <tr>
    <td colspan="2" align="center" valign="middle" style="padding:25px;">
    
    
     <div style="background:#ec2e64; border-radius:5px; padding:15px;">
     <h1 style="font-family:Arial, Helvetica, sans-serif; font-size:36px; color:#ffffff; text-transform:uppercase; margin:0; padding:0;">A Total Wellness</h1>
 
   <h1 style="font-family:Arial, Helvetica, sans-serif; font-size:30px; color:#ffffff; text-transform:uppercase; margin:0; padding:0;">Philosophy with You in Mind</h1>

<h2 style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#000000; font-weight:normal; border-bottom:solid 1px #c2204e; margin:0; padding:15px 20px; line-height:24px;">Our programs are easy to follow, short in time, and simple to use. Whether 
you are a brand-new participant to the health 
industry or a seasoned athlete, Vivacity has a program level to 
effectively suit your needs. </h2>

<h3 style="font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#e6e6e6; margin:0; padding:12px 0 0 0;">Commit. Choose a package. Feel the results.<br />
Upgrade to your new, vital life now!</h3>
     
     </div>
    
    </td>
    </tr>
    
    <tr>
    <td colspan="2" align="center" valign="middle">
    
    
    
     <h1 style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; font-size:45px; color:#51b517; text-transform:uppercase; margin:0; padding:8px 0 0 0;">Account Information</h1>

<h2 style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#5e5e5e; font-weight:normal; line-height:24px; margin:0; padding:10px 0 25px 0;">Full backoffice access will be available during our official launch date, <br />

JANUARY 23, 2016. <br />

You can still access the backend (limited view) now!  </h2>

<h3 style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#3c3c3b; margin:0; padding:0; font-weight:normal;">Username:   <span style="color:#2e7aec; padding-left:10px;">' . $username . '</span></h3>

<a href="http://www.vivacitygo.com/login?ai_bypass=true" target="_blank" style="display:block; margin:26px auto; width:120px; height:31px; background:#51b517; font-size:16px; color:#fff; text-align:center; text-transform:uppercase; font-weight:bold; line-height:33px; text-decoration:none;">Login Now</a>
     
   
    
    </td>
    </tr>
    
    <tr>
    <td colspan="2" align="center" valign="middle" style="background:#51b517; padding:10px 2px;">
    
    
    
     <h1 style="font-family:Arial, Helvetica, sans-serif; font-size:30px; color:#fff; text-transform:uppercase; margin:0; padding:0; font-weight:bold;">we\'re here to help!</h1>

<h2 style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#ffffff; font-weight:bold; margin:0; padding:5px 0;">If you run into any problems contact us and our team will be sure to take care of you. </h2>

<h3 style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#193906; line-height:18px; margin:0; padding:10px 0 0 0;">210 E. Tennessee Street Florence, AL 35630<br />

 info@makewaywellness.com<br />
Phone: 800.928.9401<br />
Fax: 615.861.8955<br /></h3>


     
   
    
    </td>
    </tr>
    <tr>
    <td colspan="2" align="center" valign="middle">
    
    
    
    <img src="http://www.vivacitygo.com/system/themes/prelaunch_lp/images/logo-enrollment.png"  alt="#"  style="width:170px; display:block; margin:10px auto;"/>

     
   
    
    </td>
    </tr>
    
</table>

    
    
    </td>
  </tr>
</table>


</body>
</html>
';
}
?>